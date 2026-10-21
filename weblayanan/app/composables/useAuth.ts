export interface AuthUser {
  id: number
  name: string
  email: string
  role: "admin" | "mahasiswa"
  nim?: string | null
  avatar?: string | null
}

export function useAuth() {
  const config = useRuntimeConfig()
  const apiBase = config.public.apiBase || "http://localhost:8000/api"

  // Simpan token di Cookie agar persist saat reload & SSR
  const token = useCookie<string | null>("auth_token", {
    maxAge: 60 * 60 * 24 * 7, // 7 hari
    path: "/",
    sameSite: "lax",
  })

  // Simpan data user di state global reaktif Nuxt
  const user = useState<AuthUser | null>("auth_user", () => null)

  const isAuthenticated = computed(() => !!token.value && !!user.value)
  const isAdmin = computed(() => user.value?.role === "admin")
  const isMahasiswa = computed(() => user.value?.role === "mahasiswa")

  // Ambil profil user saat ini dari token
  async function fetchUser(): Promise<AuthUser | null> {
    if (!token.value) {
      user.value = null
      return null
    }

    try {
      const res = await $fetch<any>(`${apiBase}/auth/me`, {
        headers: {
          Authorization: `Bearer ${token.value}`,
          Accept: "application/json",
        },
      })

      if (res.status === "success" && res.user) {
        user.value = res.user
        return res.user
      }
    } catch (err) {
      console.warn("Sesi kadaluarsa atau tidak valid:", err)
      token.value = null
      user.value = null
    }

    return null
  }

  // Login dengan Email/Username & Password (Admin / Manual)
  async function loginWithPassword(email: string, password: string):Promise<any> {
    const res = await $fetch<any>(`${apiBase}/auth/login`, {
      method: "POST",
      body: { email, password },
      headers: { Accept: "application/json" },
    })

    if (res.status === "success" && res.token) {
      token.value = res.token
      user.value = res.user
      return res
    }

    throw new Error(res.message || "Gagal masuk ke sistem.")
  }

  // Login dengan Google SSO (Mahasiswa)
  async function loginWithGoogle(payload: {
    access_token?: string
    credential?: string
    email?: string
    name?: string
    avatar?: string
    google_id?: string
  }): Promise<any> {
    const res = await $fetch<any>(`${apiBase}/auth/google`, {
      method: "POST",
      body: payload,
      headers: { Accept: "application/json" },
    })

    if (res.status === "success" && res.token) {
      token.value = res.token
      user.value = res.user
      return res
    }

    throw new Error(res.message || "Gagal login SSO Google.")
  }

  // Registrasi Mahasiswa Baru
  async function register(payload: {
    name: string
    email: string
    nim: string
    password: string
    password_confirmation: string
  }): Promise<any> {
    const res = await $fetch<any>(`${apiBase}/auth/register`, {
      method: "POST",
      body: payload,
      headers: { Accept: "application/json" },
    })

    if (res.status === "success" && res.token) {
      token.value = res.token
      user.value = res.user
      return res
    }

    throw new Error(res.message || "Gagal melakukan registrasi.")
  }

  // Logout
  async function logout(): Promise<void> {
    if (token.value) {
      try {
        await $fetch<any>(`${apiBase}/auth/logout`, {
          method: "POST",
          headers: {
            Authorization: `Bearer ${token.value}`,
            Accept: "application/json",
          },
        })
      } catch (err) {
        console.warn("Gagal revoke token logout:", err)
      }
    }

    token.value = null
    user.value = null

    if (import.meta.client) {
      localStorage.clear()
      sessionStorage.clear()
    }
  }

  return {
    user,
    token,
    isAuthenticated,
    isAdmin,
    isMahasiswa,
    fetchUser,
    loginWithPassword,
    loginWithGoogle,
    register,
    logout,
  }
}
