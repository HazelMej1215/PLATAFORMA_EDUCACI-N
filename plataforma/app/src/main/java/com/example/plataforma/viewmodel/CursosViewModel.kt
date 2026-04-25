package com.example.plataforma.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.plataforma.data.models.Curso
import com.example.plataforma.network.RetrofitClient
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.launch

data class CursosUiState(
    val isLoading: Boolean = false,
    val cursos: List<Curso> = emptyList(),
    val errorMessage: String? = null
)

class CursosViewModel : ViewModel() {
    private val _uiState = MutableStateFlow(CursosUiState())
    val uiState: StateFlow<CursosUiState> = _uiState

    fun loadCursos() {
        viewModelScope.launch {
            _uiState.value = CursosUiState(isLoading = true)
            try {
                val response = RetrofitClient.apiService.listarCursosCliente()
                if (response.isSuccessful && response.body()?.success == true) {
                    _uiState.value = CursosUiState(cursos = response.body()?.cursos ?: emptyList())
                } else {
                    _uiState.value = CursosUiState(errorMessage = "No se pudieron cargar los cursos")
                }
            } catch (e: Exception) {
                _uiState.value = CursosUiState(errorMessage = "Error de red")
            }
        }
    }
}