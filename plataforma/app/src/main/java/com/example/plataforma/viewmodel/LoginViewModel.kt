package com.example.plataforma.viewmodel

import android.content.Context
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.plataforma.data.SessionManager
import com.example.plataforma.network.RetrofitClient
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.launch

data class LoginUiState(
    val isLoading: Boolean = false,
    val isSuccess: Boolean = false,
    val errorMessage: String? = null
)

class LoginViewModel(private val context: Context) : ViewModel() {
    private val sessionManager = SessionManager(context)
    private val _uiState = MutableStateFlow(LoginUiState())
    val uiState: StateFlow<LoginUiState> = _uiState

    fun login(email: String, password: String) {
        viewModelScope.launch {
            _uiState.value = LoginUiState(isLoading = true)
            try {
                val loginResponse = RetrofitClient.apiService.login(correo = email, contrasena = password)

                if (!loginResponse.isSuccessful || loginResponse.body()?.success != true) {
                    val msg = loginResponse.body()?.message ?: "Credenciales incorrectas"
                    _uiState.value = LoginUiState(errorMessage = msg)
                    return@launch
                }

                val body = loginResponse.body()!!
                val sessionId = body.session_id
                val nombre = body.nombre
                val rol = body.rol

                if (sessionId == null || nombre == null || rol == null) {
                    _uiState.value = LoginUiState(errorMessage = "Respuesta del servidor incompleta")
                    return@launch
                }

                // Guardar el session_id en el interceptor de Retrofit
                RetrofitClient.setSessionId(sessionId)

                // Guardar datos del usuario en DataStore
                sessionManager.saveSession(nombre, rol)

                _uiState.value = LoginUiState(isSuccess = true)
            } catch (e: Exception) {
                _uiState.value = LoginUiState(errorMessage = "Error de red: ${e.localizedMessage}")
            }
        }
    }

    fun resetState() {
        _uiState.value = LoginUiState()
    }
}