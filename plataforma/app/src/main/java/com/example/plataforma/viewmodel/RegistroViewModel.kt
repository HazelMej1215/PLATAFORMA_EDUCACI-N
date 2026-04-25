package com.example.plataforma.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.plataforma.network.RetrofitClient
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.launch

data class RegistroUiState(
    val isLoading: Boolean = false,
    val isSuccess: Boolean = false,
    val errorMessage: String? = null
)

class RegistroViewModel : ViewModel() {
    private val _uiState = MutableStateFlow(RegistroUiState())
    val uiState: StateFlow<RegistroUiState> = _uiState

    fun register(nombre: String, correo: String, password: String) {
        viewModelScope.launch {
            _uiState.value = RegistroUiState(isLoading = true)
            try {
                val response = RetrofitClient.apiService.registro(
                    nombre = nombre,
                    correo = correo,
                    contrasena = password
                )
                if (response.isSuccessful && response.body()?.success == true) {
                    _uiState.value = RegistroUiState(isSuccess = true)
                } else {
                    val msg = response.body()?.message ?: "Error en el registro"
                    _uiState.value = RegistroUiState(errorMessage = msg)
                }
            } catch (e: Exception) {
                _uiState.value = RegistroUiState(errorMessage = "Error de red: ${e.localizedMessage}")
            }
        }
    }

    fun resetState() {
        _uiState.value = RegistroUiState()
    }
}