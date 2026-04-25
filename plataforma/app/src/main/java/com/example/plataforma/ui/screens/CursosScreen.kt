package com.example.plataforma.ui.screens

import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.School
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import androidx.lifecycle.viewmodel.compose.viewModel
import androidx.navigation.NavHostController
import coil.compose.rememberAsyncImagePainter
import com.example.plataforma.viewmodel.CursosViewModel

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun CursosScreen(navController: NavHostController) {
    val viewModel: CursosViewModel = viewModel()
    val uiState by viewModel.uiState.collectAsState()

    LaunchedEffect(Unit) {
        viewModel.loadCursos()
    }

    Scaffold(
        topBar = { TopAppBar(title = { Text("Mis Cursos") }) }
    ) { paddingValues ->
        LazyColumn(
            modifier = Modifier.padding(paddingValues),
            contentPadding = PaddingValues(8.dp),
            verticalArrangement = Arrangement.spacedBy(8.dp)
        ) {
            items(uiState.cursos) { curso ->
                Card(
                    modifier = Modifier.fillMaxWidth().clickable {
                        navController.navigate("verCurso/${curso.id}")
                    }
                ) {
                    Row(
                        verticalAlignment = Alignment.CenterVertically,
                        modifier = Modifier.padding(8.dp)
                    ) {
                        if (curso.imagen != null) {
                            androidx.compose.foundation.Image(
                                painter = rememberAsyncImagePainter("https://red-hedgehog-742097.hostingersite.com/uploads/cursos/${curso.imagen}"),
                                contentDescription = null,
                                modifier = Modifier.size(80.dp)
                            )
                        } else {
                            Box(modifier = Modifier.size(80.dp), contentAlignment = Alignment.Center) {
                                Icon(Icons.Default.School, contentDescription = null)
                            }
                        }
                        Text(curso.nombre, modifier = Modifier.padding(16.dp))
                    }
                }
            }
            if (uiState.isLoading) item { CircularProgressIndicator(modifier = Modifier.fillMaxWidth()) }
            if (uiState.errorMessage != null) item { Text(uiState.errorMessage!!, color = MaterialTheme.colorScheme.error) }
        }
    }
}