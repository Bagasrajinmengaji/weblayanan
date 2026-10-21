export default defineNuxtRouteMiddleware(async (to) => {
  const { user, token, fetchUser, isAdmin } = useAuth()

  // Jika ada token tapi profil belum dimuat, coba fetch profil
  if (token.value && !user.value) {
    await fetchUser()
  }

  // Jika tidak terautentikasi atau bukan admin, redirect ke login admin
  if (!token.value || !user.value || !isAdmin.value) {
    return navigateTo({
      path: "/login",
      query: {
        role: "admin",
        redirect: to.fullPath,
      },
    })
  }
})
