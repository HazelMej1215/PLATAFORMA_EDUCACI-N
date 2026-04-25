package com.example.plataforma.data

import android.content.Context
import androidx.datastore.core.DataStore
import androidx.datastore.preferences.core.Preferences
import androidx.datastore.preferences.core.edit
import androidx.datastore.preferences.core.stringPreferencesKey
import androidx.datastore.preferences.preferencesDataStore
import kotlinx.coroutines.flow.Flow
import kotlinx.coroutines.flow.map

private val Context.dataStore: DataStore<Preferences> by preferencesDataStore("session")

class SessionManager(private val context: Context) {
    companion object {
        private val NOMBRE = stringPreferencesKey("nombre")
        private val ROL = stringPreferencesKey("rol")
        private val LOGGED = stringPreferencesKey("logged")
        private val TEMA = stringPreferencesKey("tema")
    }

    suspend fun saveSession(nombre: String, rol: String) {
        context.dataStore.edit { prefs ->
            prefs[NOMBRE] = nombre
            prefs[ROL] = rol
            prefs[LOGGED] = "true"
        }
    }

    suspend fun clearSession() {
        context.dataStore.edit { it.clear() }
    }

    fun isLoggedIn(): Flow<Boolean> = context.dataStore.data.map { prefs ->
        prefs[LOGGED] == "true"
    }

    fun getUserName(): Flow<String?> = context.dataStore.data.map { prefs ->
        prefs[NOMBRE]
    }

    suspend fun saveTheme(theme: String) {
        context.dataStore.edit { prefs ->
            prefs[TEMA] = theme
        }
    }

    fun getTheme(): Flow<String?> = context.dataStore.data.map { prefs ->
        prefs[TEMA]
    }
}