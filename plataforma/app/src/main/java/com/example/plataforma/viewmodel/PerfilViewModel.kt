package com.example.plataforma.viewmodel

import android.content.Context
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.plataforma.data.SessionManager
import com.example.plataforma.data.models.Perfil
import com.example.plataforma.network.RetrofitClient
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.launch

data class PerfilUiState(
    val isLoading: Boolean = false,
    val perfil: Perfil? = null,
    val updateSuccess: Boolean = false,
    val errorMessage: String? = null
)

class PerfilViewModel(private val context: Context) : ViewModel() {
    private val sessionManager = SessionManager(context)
    private val _uiState = MutableStateFlow(PerfilUiState())
    val uiState: StateFlow<PerfilUiState> = _uiState

    fun loadPerfil() {
        viewModelScope.launch {
            _uiState.value = PerfilUiState(isLoading = true)
            try {
                val response = RetrofitClient.apiService.obtenerPerfil()
                if (response.isSuccessful && response.body()?.success == true) {
                    _uiState.value = PerfilUiState(perfil = response.body()?.perfil)
                } else {
                    _uiState.value = PerfilUiState(errorMessage = "No se pudo cargar el perfil")
                }
            } catch (e: Exception) {
                _uiState.value = PerfilUiState(errorMessage = "Error de red")
            }
        }
    }

    fun updatePerfil(nuevaPassword: String? = null, tema: String? = null, imagenBase64: String? = null) {
        viewModelScope.launch {
            _uiState.value = PerfilUiState(isLoading = true)
            try {
                val response = RetrofitClient.apiService.actualizarPerfil(
                    nuevaContrasena = nuevaPassword,
                    tema = tema,
                    imagen = imagenBase64
                )
                if (response.isSuccessful && response.body()?.success == true) {
                    if (tema != null) sessionManager.saveTheme(tema)
                    _uiState.value = PerfilUiState(perfil = _uiState.value.perfil, updateSuccess = true)
                } else {
                    _uiState.value = PerfilUiState(errorMessage = response.body()?.message ?: "Error al actualizar")
                }
            } catch (e: Exception) {
                _uiState.value = PerfilUiState(errorMessage = "Error de red")
            }
        }
    }

    fun resetUpdate() {
        _uiState.value = _uiState.value.copy(updateSuccess = false)
    }
}

class PerfilViewModelFactory(private val context: Context) : androidx.lifecycle.ViewModelProvider.Factory {
    override fun <T : androidx.lifecycle.ViewModel> create(modelClass: Class<T>): T {
        @Suppress("UNCHECKED_CAST")
        return PerfilViewModel(context) as T
    }
}