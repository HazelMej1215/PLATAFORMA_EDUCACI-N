package com.example.plataforma.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.plataforma.data.models.Video
import com.example.plataforma.network.RetrofitClient
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.launch

data class CursoUiState(
    val isLoading: Boolean = false,
    val videos: List<Video> = emptyList(),
    val progreso: Map<Int, Boolean> = emptyMap(),
    val videoActual: Video? = null,
    val cursoCompletado: Boolean = false,
    val errorMessage: String? = null
)

class CursoViewModel : ViewModel() {
    private val _uiState = MutableStateFlow(CursoUiState())
    val uiState: StateFlow<CursoUiState> = _uiState

    fun loadCurso(cursoId: Int) {
        viewModelScope.launch {
            _uiState.value = CursoUiState(isLoading = true)
            try {
                val idStr = cursoId.toString()
                val videosResponse = RetrofitClient.apiService.videosCurso(cursoId = idStr)
                val progresoResponse = RetrofitClient.apiService.obtenerProgreso(cursoId = idStr)
                if (videosResponse.isSuccessful && videosResponse.body()?.success == true) {
                    val videos = videosResponse.body()?.videos ?: emptyList()
                    val progresoMap = progresoResponse.body()?.progreso?.associate { it.id to (it.visto == 1) } ?: emptyMap()
                    val completado = videos.isNotEmpty() && videos.all { progresoMap[it.id] == true }
                    val primerNoVisto = videos.find { progresoMap[it.id] != true }
                    val videoActual = if (primerNoVisto != null) primerNoVisto else videos.lastOrNull()
                    _uiState.value = CursoUiState(
                        videos = videos,
                        progreso = progresoMap,
                        videoActual = videoActual,
                        cursoCompletado = completado
                    )
                } else {
                    _uiState.value = CursoUiState(errorMessage = "Error cargando contenido")
                }
            } catch (e: Exception) {
                _uiState.value = CursoUiState(errorMessage = "Error de red")
            }
        }
    }

    fun marcarVisto(videoId: Int, cursoId: Int) {
        viewModelScope.launch {
            try {
                val response = RetrofitClient.apiService.marcarVisto(videoId = videoId)
                if (response.isSuccessful && response.body()?.success == true) {
                    val progresoResponse = RetrofitClient.apiService.obtenerProgreso(cursoId = cursoId.toString())
                    if (progresoResponse.isSuccessful && progresoResponse.body()?.success == true) {
                        val nuevoProgreso = progresoResponse.body()?.progreso?.associate { it.id to (it.visto == 1) } ?: emptyMap()
                        val completado = _uiState.value.videos.isNotEmpty() && _uiState.value.videos.all { nuevoProgreso[it.id] == true }
                        _uiState.value = _uiState.value.copy(
                            progreso = nuevoProgreso,
                            cursoCompletado = completado
                        )
                    }
                }
            } catch (e: Exception) {
                // ignora
            }
        }
    }

    fun setVideoActual(video: Video) {
        _uiState.value = _uiState.value.copy(videoActual = video)
    }
}