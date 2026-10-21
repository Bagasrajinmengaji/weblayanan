export default defineNuxtRouteMiddleware(async (to) => {
  const { user, token, fetchUser, isAdmin } = useAuth()

  // Jika ada token tersimpan di cookie tapi user state belum siap, fetch user
  if (token.value && !user.value) {
    await fetchUser()
  }

  // Jika belum login, arahkan langsung ke halaman login form
  if (!token.value || !user.value) {
    return navigateTo({
      path: "/login",
      query: {
        redirect: to.fullPath !== "/" ? to.fullPath : undefined,
      },
    })
  }

  // Jika admin membuka halaman mahasiswa, arahkan ke dashboard admin
  if (isAdmin.value && to.path === "/") {
    return navigateTo("/admin")
  }
})
