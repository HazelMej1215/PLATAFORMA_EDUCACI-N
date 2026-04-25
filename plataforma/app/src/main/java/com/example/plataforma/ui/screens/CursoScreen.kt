package com.example.plataforma.ui.screens

import android.webkit.WebView
import android.webkit.WebViewClient
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.CheckCircle
import androidx.compose.material.icons.filled.Star
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.viewinterop.AndroidView
import androidx.compose.ui.unit.dp
import androidx.lifecycle.viewmodel.compose.viewModel
import androidx.navigation.NavHostController
import com.example.plataforma.data.models.CertificadoData
import com.example.plataforma.network.RetrofitClient
import com.example.plataforma.viewmodel.CursoViewModel
import kotlinx.coroutines.launch

fun String.toYoutubeEmbedUrl(): String {
    if (contains("youtube.com/embed/")) return this
    val pattern = "(?:youtube\\.com\\/watch\\?v=|youtu\\.be\\/|youtube\\.com\\/shorts\\/)([a-zA-Z0-9_-]+)".toRegex()
    val match = pattern.find(this)
    return if (match != null) {
        "https://www.youtube.com/embed/${match.groupValues[1]}"
    } else {
        this
    }
}
@Composable
fun CursoScreen(navController: NavHostController, cursoId: Int) {
    val viewModel: CursoViewModel = viewModel()
    val uiState by viewModel.uiState.collectAsState()
    val scope = rememberCoroutineScope()
    var showRatingDialog by remember { mutableStateOf(false) }
    var certificadoData by remember { mutableStateOf<CertificadoData?>(null) }

    // Cargar el curso cuando se abre la pantalla
    LaunchedEffect(cursoId) {
        viewModel.loadCurso(cursoId)
    }

    // Mostrar diálogo de calificación si el curso está completado
    LaunchedEffect(uiState.cursoCompletado) {
        if (uiState.cursoCompletado && !showRatingDialog) {
            showRatingDialog = true
        }
    }

    // Diálogo del certificado
    if (certificadoData != null) {
        CertificadoDialog(certificadoData!!) { certificadoData = null }
    }

    // Diálogo de calificación
    if (showRatingDialog) {
        RatingDialog(
            onDismiss = { showRatingDialog = false },
            onCalificar = { puntuacion, comentario ->
                scope.launch {
                    val califResponse = RetrofitClient.apiService.calificarCurso(
                        cursoId = cursoId.toString(),
                        puntuacion = puntuacion,
                        comentario = comentario
                    )
                    if (califResponse.isSuccessful && califResponse.body()?.success == true) {
                        val certResponse = RetrofitClient.apiService.generarCertificado(cursoId = cursoId.toString())
                        if (certResponse.isSuccessful && certResponse.body()?.success == true) {
                            certificadoData = certResponse.body()?.certificado
                        }
                    }
                    showRatingDialog = false
                }
            }
        )
    }

    Column(modifier = Modifier.fillMaxSize()) {
        // Reproductor de video
        uiState.videoActual?.let { video ->
            Card(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(8.dp),
                elevation = CardDefaults.cardElevation(defaultElevation = 4.dp)
            ) {
                AndroidView(
                    factory = { ctx ->
                        WebView(ctx).apply {
                            settings.apply {
                                javaScriptEnabled = true
                                domStorageEnabled = true
                                mediaPlaybackRequiresUserGesture = false   // reproduce automáticamente (si es posible)
                                allowFileAccess = true
                                allowContentAccess = true
                                useWideViewPort = true
                                loadWithOverviewMode = true
                                setSupportZoom(true)
                                builtInZoomControls = true
                                displayZoomControls = false
                                // Importante para soporte de video en algunas versiones
                                setPluginState(android.webkit.WebSettings.PluginState.ON)
                            }
                            webViewClient = WebViewClient()  // maneja enlaces dentro del mismo WebView
                            webChromeClient = android.webkit.WebChromeClient()  // necesario para video
                            // Forzar modo escritorio para que YouTube no solicite app
                            settings.userAgentString = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36"
                            loadUrl(video.url.toYoutubeEmbedUrl())
                        }
                    },
                    modifier = Modifier
                        .fillMaxWidth()
                        .height(250.dp)
                        .padding(8.dp)
                )
            }

            // Botón "Marcar como visto" (solo si el video actual no está visto)
            if (uiState.progreso[video.id] != true) {
                Button(
                    onClick = { viewModel.marcarVisto(videoId = video.id, cursoId = cursoId) },
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(horizontal = 16.dp, vertical = 8.dp)
                ) {
                    Text("Marcar como visto")
                }
            }
        }

        // Lista de lecciones
        LazyColumn(
            modifier = Modifier.weight(1f),
            contentPadding = PaddingValues(8.dp),
            verticalArrangement = Arrangement.spacedBy(8.dp)
        ) {
            items(uiState.videos) { video ->
                val visto = uiState.progreso[video.id] == true
                val bloqueado = !visto && video.orden > 1 &&
                        uiState.progreso[uiState.videos.getOrNull(video.orden - 2)?.id ?: -1] != true

                Card(
                    modifier = Modifier
                        .fillMaxWidth()
                        .clickable(enabled = !bloqueado) {
                            if (!bloqueado) viewModel.setVideoActual(video)
                        },
                    colors = CardDefaults.cardColors(
                        containerColor = when {
                            visto -> MaterialTheme.colorScheme.primaryContainer
                            bloqueado -> MaterialTheme.colorScheme.surfaceVariant
                            else -> MaterialTheme.colorScheme.secondaryContainer
                        }
                    )
                ) {
                    Row(
                        modifier = Modifier
                            .fillMaxWidth()
                            .padding(12.dp),
                        verticalAlignment = Alignment.CenterVertically,
                        horizontalArrangement = Arrangement.SpaceBetween
                    ) {
                        Text(
                            text = "${video.orden}. ${video.titulo}",
                            modifier = Modifier.weight(1f),
                            color = when {
                                visto -> MaterialTheme.colorScheme.onPrimaryContainer
                                bloqueado -> MaterialTheme.colorScheme.onSurfaceVariant
                                else -> MaterialTheme.colorScheme.onSecondaryContainer
                            }
                        )
                        if (visto) {
                            Icon(
                                Icons.Default.CheckCircle,
                                contentDescription = "Visto",
                                tint = MaterialTheme.colorScheme.primary
                            )
                        }
                    }
                }
            }
        }
    }
}

@Composable
fun RatingDialog(onDismiss: () -> Unit, onCalificar: (Int, String) -> Unit) {
    var rating by remember { mutableStateOf(0) }
    var comentario by remember { mutableStateOf("") }

    AlertDialog(
        onDismissRequest = onDismiss,
        title = { Text("Califica este curso") },
        text = {
            Column(
                horizontalAlignment = Alignment.CenterHorizontally,
                modifier = Modifier.fillMaxWidth()
            ) {
                Text("Puntuación: $rating estrellas")
                Spacer(modifier = Modifier.height(8.dp))
                Row(
                    horizontalArrangement = Arrangement.Center,
                    modifier = Modifier.fillMaxWidth()
                ) {
                    repeat(5) { idx ->
                        IconButton(onClick = { rating = idx + 1 }) {
                            Icon(
                                Icons.Default.Star,
                                tint = if (idx < rating) MaterialTheme.colorScheme.primary
                                else MaterialTheme.colorScheme.onSurface.copy(alpha = 0.3f),
                                contentDescription = null
                            )
                        }
                    }
                }
                Spacer(modifier = Modifier.height(16.dp))
                OutlinedTextField(
                    value = comentario,
                    onValueChange = { comentario = it },
                    label = { Text("Comentario (obligatorio)") },
                    modifier = Modifier.fillMaxWidth(),
                    minLines = 3,
                    maxLines = 5
                )
            }
        },
        confirmButton = {
            Button(
                onClick = {
                    if (rating > 0 && comentario.isNotBlank()) {
                        onCalificar(rating, comentario)
                    }
                }
            ) {
                Text("Enviar")
            }
        },
        dismissButton = {
            TextButton(onClick = onDismiss) {
                Text("Cancelar")
            }
        }
    )
}

@Composable
fun CertificadoDialog(certData: CertificadoData, onDismiss: () -> Unit) {
    AlertDialog(
        onDismissRequest = onDismiss,
        title = { Text("Certificado de finalización") },
        text = {
            Column(
                horizontalAlignment = Alignment.CenterHorizontally,
                modifier = Modifier.fillMaxWidth()
            ) {
                Text("Otorgado a:", style = MaterialTheme.typography.bodyMedium)
                Text(certData.usuario, style = MaterialTheme.typography.titleMedium, color = MaterialTheme.colorScheme.primary)
                Spacer(modifier = Modifier.height(8.dp))
                Text("Por haber completado el curso:", style = MaterialTheme.typography.bodyMedium)
                Text(certData.curso, style = MaterialTheme.typography.titleSmall)
                Spacer(modifier = Modifier.height(8.dp))
                Text("Fecha: ${certData.fecha}", style = MaterialTheme.typography.bodySmall)
                Text("Código: ${certData.codigo}", style = MaterialTheme.typography.bodySmall)
                Spacer(modifier = Modifier.height(12.dp))
                Text(
                    "Puedes tomar una captura de pantalla como certificado.",
                    style = MaterialTheme.typography.bodySmall,
                    color = MaterialTheme.colorScheme.onSurfaceVariant
                )
            }
        },
        confirmButton = {
            TextButton(onClick = onDismiss) {
                Text("Cerrar")
            }
        }
    )
}