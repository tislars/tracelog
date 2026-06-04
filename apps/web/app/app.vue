<template>
  <div class="site">
    <header class="site-header">
      <div class="container site-header__inner">
        <a href="/" class="site-header__logo">WP Headless POC</a>
        <nav class="site-header__nav">
          <a href="/">Home</a>
          <NuxtLink to="/team">Team</NuxtLink>
          <NuxtLink to="/contact">Contact</NuxtLink>
        </nav>
      </div>
    </header>

    <NuxtRouteAnnouncer />
    <NuxtPage />

    <footer class="site-footer">
      <div class="container">
        <p>&copy; {{ new Date().getFullYear() }} WP Headless POC</p>
      </div>
    </footer>
  </div>
</template>

<style>
.site {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.site-header {
  background: #fff;
  border-bottom: 1px solid #e5e7eb;
  position: sticky;
  top: 0;
  z-index: 100;
}

.site-header__inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-block: 1rem;
}

.site-header__logo {
  font-size: 1.25rem;
  font-weight: 700;
  color: #1a1a2e;
  text-decoration: none;
  letter-spacing: -0.025em;
}

.site-header__nav {
  display: flex;
  gap: 1.5rem;
}

.site-header__nav a {
  color: #374151;
  text-decoration: none;
  font-size: 0.9375rem;
  font-weight: 500;
}

.site-header__nav a:hover {
  color: #2563eb;
}

.site-footer {
  margin-top: auto;
  background: #1a1a2e;
  color: #9ca3af;
  padding-block: 2rem;
  font-size: 0.875rem;
}
</style>
