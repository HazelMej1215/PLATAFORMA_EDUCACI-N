package com.example.plataforma.network

import okhttp3.Interceptor
import okhttp3.OkHttpClient
import okhttp3.logging.HttpLoggingInterceptor
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory
import java.util.concurrent.TimeUnit


object RetrofitClient {
    private const val BASE_URL = "https://red-hedgehog-742097.hostingersite.com/"
    private var sessionId: String? = null

    private val sessionInterceptor = Interceptor { chain ->
        var request = chain.request()
        if (sessionId != null) {
            val url = request.url.newBuilder()
                .addQueryParameter("session_id", sessionId)
                .build()
            request = request.newBuilder().url(url).build()
        }
        chain.proceed(request)
    }

    private val client = OkHttpClient.Builder()
        .addInterceptor(sessionInterceptor)
        .addInterceptor(HttpLoggingInterceptor().apply { level = HttpLoggingInterceptor.Level.BODY })
        .connectTimeout(30, TimeUnit.SECONDS)
        .readTimeout(30, TimeUnit.SECONDS)
        .build()

    val apiService: ApiService by lazy {
        Retrofit.Builder()
            .baseUrl(BASE_URL)
            .client(client)
            .addConverterFactory(GsonConverterFactory.create())
            .build()
            .create(ApiService::class.java)
    }

    fun setSessionId(id: String) {
        sessionId = id
    }
}