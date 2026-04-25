package com.example.plataforma.ui.screens

import androidx.compose.foundation.layout.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.input.PasswordVisualTransformation
import androidx.compose.ui.unit.dp
import androidx.lifecycle.viewmodel.compose.viewModel
import androidx.navigation.NavHostController
import com.example.plataforma.viewmodel.RegistroViewModel

@Composable
fun RegistroScreen(navController: NavHostController) {
    val viewModel: RegistroViewModel = viewModel()
    val uiState by viewModel.uiState.collectAsState()
    var nombre by remember { mutableStateOf("") }
    var email by remember { mutableStateOf("") }
    var password by remember { mutableStateOf("") }

    LaunchedEffect(uiState.isSuccess) {
        if (uiState.isSuccess) {
            navController.popBackStack()
            viewModel.resetState()
        }
    }

    Column(
        modifier = Modifier.fillMaxSize().padding(24.dp),
        horizontalAlignment = Alignment.CenterHorizontally,
        verticalArrangement = Arrangement.Center
    ) {
        Text("Crear cuenta", style = MaterialTheme.typography.headlineMedium)
        Spacer(modifier = Modifier.height(16.dp))
        OutlinedTextField(value = nombre, onValueChange = { nombre = it }, label = { Text("Nombre completo") }, modifier = Modifier.fillMaxWidth())
        OutlinedTextField(value = email, onValueChange = { email = it }, label = { Text("Correo electrónico") }, modifier = Modifier.fillMaxWidth())
        OutlinedTextField(value = password, onValueChange = { password = it }, label = { Text("Contraseña") }, visualTransformation = PasswordVisualTransformation(), modifier = Modifier.fillMaxWidth())
        Spacer(modifier = Modifier.height(16.dp))
        Button(onClick = { viewModel.register(nombre, email, password) }, modifier = Modifier.fillMaxWidth(), enabled = !uiState.isLoading) {
            if (uiState.isLoading) CircularProgressIndicator(modifier = Modifier.size(20.dp)) else Text("Registrarse")
        }
        if (uiState.errorMessage != null) {
            Text(uiState.errorMessage!!, color = MaterialTheme.colorScheme.error)
        }
    }
}