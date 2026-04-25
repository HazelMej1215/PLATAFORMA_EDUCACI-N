package com.example.plataforma.data.models

data class LoginResponse(
    val success: Boolean,
    val redirect: String? = null,
    val message: String? = null,
    val session_id: String? = null,
    val nombre: String? = null,
    val rol: String? = null
)

data class SessionResponse(
    val logged: Boolean,
    val rol: String? = null,
    val nombre: String? = null
)

data class GenericResponse(
    val success: Boolean,
    val message: String? = null
)

data class PerfilResponse(
    val success: Boolean,
    val perfil: Perfil? = null,
    val message: String? = null
)

data class Perfil(
    val nombre: String,
    val correo: String,
    val imagen_perfil: String?,
    val tema: String
)

data class CursosResponse(
    val success: Boolean,
    val cursos: List<Curso> = emptyList()
)

data class Curso(
    val id: Int,
    val nombre: String,
    val imagen: String?
)

data class VideosResponse(
    val success: Boolean,
    val videos: List<Video> = emptyList()
)

data class Video(
    val id: Int,
    val titulo: String,
    val descripcion: String?,
    val orden: Int,
    val url: String
)

data class ProgresoResponse(
    val success: Boolean,
    val progreso: List<ProgresoVideo> = emptyList()
)

data class ProgresoVideo(
    val id: Int,
    val orden: Int,
    val visto: Int
)

data class CertificadoResponse(
    val success: Boolean,
    val certificado: CertificadoData? = null,
    val message: String? = null
)

data class CertificadoData(
    val usuario: String,
    val curso: String,
    val fecha: String,
    val codigo: String
)