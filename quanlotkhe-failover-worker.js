addEventListener('fetch', event => {
    event.respondWith(handleRequest(event.request))
  })
  
  async function handleRequest(request) {
    const response = await fetch(request)
    if (response.status >= 500 && response.status < 600) {
      // Hiển thị trang bảo trì khi lỗi server
      const maintenanceResponse = await fetch('https://carl125.github.io/quanlotkhe-maintenance/')
      const maintenanceBody = await maintenanceResponse.text()
      return new Response(maintenanceBody, {
        status: maintenanceResponse.status,
        statusText: maintenanceResponse.statusText,
        headers: {
          'Content-Type': 'text/html',
        },
      })
    }
    return response
  }
  