package com.example.plataforma

import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.material3.Surface
import androidx.compose.ui.Modifier
import androidx.navigation.compose.NavHost
import androidx.navigation.compose.composable
import androidx.navigation.compose.rememberNavController
import com.example.plataforma.ui.screens.*
import com.example.plataforma.ui.theme.PlataformaTheme

class MainActivity : ComponentActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContent {
            PlataformaTheme {
                Surface(modifier = Modifier.fillMaxSize()) {
                    val navController = rememberNavController()
                    NavHost(navController = navController, startDestination = "login") {
                        composable("login") { LoginScreen(navController) }
                        composable("registro") { RegistroScreen(navController) }
                        composable("dashboard") { CursosScreen(navController) }
                        composable("perfil") { PerfilScreen(navController) }
                        composable("verCurso/{cursoId}") { backStackEntry ->
                            val cursoId = backStackEntry.arguments?.getString("cursoId")?.toIntOrNull() ?: 0
                            CursoScreen(navController, cursoId)
                        }
                    }
                }
            }
        }
    }
}