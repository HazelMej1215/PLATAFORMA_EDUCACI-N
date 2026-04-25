package com.example.plataforma.ui.screens

import android.net.Uri
import androidx.activity.compose.rememberLauncherForActivityResult
import androidx.activity.result.contract.ActivityResultContracts
import androidx.compose.foundation.Image
import androidx.compose.foundation.layout.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.input.PasswordVisualTransformation
import androidx.compose.ui.unit.dp
import androidx.lifecycle.viewmodel.compose.viewModel
import androidx.navigation.NavHostController
import coil.compose.rememberAsyncImagePainter
import com.example.plataforma.viewmodel.PerfilViewModel
import com.example.plataforma.viewmodel.PerfilViewModelFactory
import java.util.Base64
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Person
@Composable
fun PerfilScreen(navController: NavHostController) {
    val context = LocalContext.current
    val viewModel: PerfilViewModel = viewModel(factory = PerfilViewModelFactory(context))
    val uiState by viewModel.uiState.collectAsState()
    var nuevaPassword by remember { mutableStateOf("") }
    var confirmarPassword by remember { mutableStateOf("") }
    var temaSeleccionado by remember { mutableStateOf("claro") }
    var imagenBase64 by remember { mutableStateOf<String?>(null) }

    LaunchedEffect(Unit) {
        viewModel.loadPerfil()
    }

    LaunchedEffect(uiState.perfil) {
        temaSeleccionado = uiState.perfil?.tema ?: "claro"
    }

    val launcher = rememberLauncherForActivityResult(ActivityResultContracts.GetContent()) { uri: Uri? ->
        uri?.let {
            val inputStream = context.contentResolver.openInputStream(it)
            val bytes = inputStream?.readBytes()
            imagenBase64 = Base64.getEncoder().encodeToString(bytes).let { encoded -> "data:image/jpeg;base64,$encoded" }
        }
    }

    Column(
        modifier = Modifier.fillMaxSize().padding(16.dp),
        verticalArrangement = Arrangement.spacedBy(12.dp)
    ) {
        Text("Mi Perfil", style = MaterialTheme.typography.headlineMedium)
        if (uiState.perfil != null) {
            if (uiState.perfil?.imagen_perfil != null) {
                Image(
                    painter = rememberAsyncImagePainter("https://red-hedgehog-742097.hostingersite.com/uploads/${uiState.perfil?.imagen_perfil}"),
                    contentDescription = null,
                    modifier = Modifier.size(100.dp)
                )
            } else {
                Icon(androidx.compose.material.icons.Icons.Default.Person, contentDescription = null, modifier = Modifier.size(100.dp))
            }
            Button(onClick = { launcher.launch("image/*") }) { Text("Seleccionar foto") }
            Text("Nombre: ${uiState.perfil?.nombre}")
            Text("Correo: ${uiState.perfil?.correo}")
            OutlinedTextField(value = nuevaPassword, onValueChange = { nuevaPassword = it }, label = { Text("Nueva contraseña") }, visualTransformation = PasswordVisualTransformation())
            OutlinedTextField(value = confirmarPassword, onValueChange = { confirmarPassword = it }, label = { Text("Confirmar") }, visualTransformation = PasswordVisualTransformation())
            Row(verticalAlignment = Alignment.CenterVertically) {
                Text("Tema: ")
                RadioButton(selected = temaSeleccionado == "claro", onClick = { temaSeleccionado = "claro" })
                Text("Claro")
                RadioButton(selected = temaSeleccionado == "oscuro", onClick = { temaSeleccionado = "oscuro" })
                Text("Oscuro")
            }
            Button(onClick = {
                if (nuevaPassword == confirmarPassword) {
                    viewModel.updatePerfil(
                        nuevaPassword = nuevaPassword.takeIf { it.isNotBlank() },
                        tema = temaSeleccionado,
                        imagenBase64 = imagenBase64
                    )
                }
            }) {
                Text("Guardar cambios")
            }
            if (uiState.updateSuccess) {
                Text("Actualizado correctamente", color = MaterialTheme.colorScheme.primary)
                viewModel.resetUpdate()
            }
            if (uiState.errorMessage != null) Text(uiState.errorMessage!!, color = MaterialTheme.colorScheme.error)
        } else if (uiState.isLoading) CircularProgressIndicator()
    }
}