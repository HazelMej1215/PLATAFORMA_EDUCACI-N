package com.example.plataforma.network

import com.example.plataforma.data.models.*
import retrofit2.Response
import retrofit2.http.*

interface ApiService {
    @FormUrlEncoded
    @POST("api.php")
    suspend fun login(
        @Field("action") action: String = "login",
        @Field("correo") correo: String,
        @Field("contraseña") contrasena: String
    ): Response<LoginResponse>

    @GET("api.php")
    suspend fun verificarSesion(
        @Query("action") action: String = "verificar_sesion"
    ): Response<SessionResponse>

    @GET("api.php")
    suspend fun logout(@Query("action") action: String = "logout"): Response<Unit>

    @FormUrlEncoded
    @POST("api.php")
    suspend fun registro(
        @Field("action") action: String = "registro",
        @Field("nombre") nombre: String,
        @Field("correo") correo: String,
        @Field("contraseña") contrasena: String
    ): Response<LoginResponse>

    @GET("api.php")
    suspend fun obtenerPerfil(
        @Query("action") action: String = "obtener_perfil"
    ): Response<PerfilResponse>

    @FormUrlEncoded
    @POST("api.php")
    suspend fun actualizarPerfil(
        @Field("action") action: String = "actualizar_perfil",
        @Field("nueva_contraseña") nuevaContrasena: String? = null,
        @Field("tema") tema: String? = null,
        @Field("imagen") imagen: String? = null
    ): Response<GenericResponse>

    @GET("api.php")
    suspend fun listarCursosCliente(
        @Query("action") action: String = "listar_cursos_cliente"
    ): Response<CursosResponse>

    @GET("api.php")
    suspend fun videosCurso(
        @Query("action") action: String = "videos_curso",
        @Query("curso_id") cursoId: String
    ): Response<VideosResponse>

    @GET("api.php")
    suspend fun obtenerProgreso(
        @Query("action") action: String = "obtener_progreso",
        @Query("curso_id") cursoId: String
    ): Response<ProgresoResponse>

    @FormUrlEncoded
    @POST("api.php")
    suspend fun marcarVisto(
        @Field("action") action: String = "marcar_visto",
        @Field("video_id") videoId: Int
    ): Response<GenericResponse>

    @FormUrlEncoded
    @POST("api.php")
    suspend fun calificarCurso(
        @Field("action") action: String = "calificar_curso",
        @Field("curso_id") cursoId: String,
        @Field("puntuacion") puntuacion: Int,
        @Field("comentario") comentario: String
    ): Response<GenericResponse>

    @GET("api.php")
    suspend fun generarCertificado(
        @Query("action") action: String = "generar_certificado",
        @Query("curso_id") cursoId: String
    ): Response<CertificadoResponse>
}