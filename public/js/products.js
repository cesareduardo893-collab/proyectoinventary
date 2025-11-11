// resources/js/products.js
document.addEventListener('DOMContentLoaded', function() {
    // Configuración de SweetAlert para mensajes del sistema
    const setupSweetAlerts = () => {
        // Mostrar mensajes de éxito
        if (typeof successMessage !== 'undefined' && successMessage) {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: successMessage,
                background: '#f7fafc',
                confirmButtonColor: '#000000ff',
                timer: 3000,
                timerProgressBar: true
            });
        }

        // Mostrar mensajes de error
        if (typeof errorMessage !== 'undefined' && errorMessage) {
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: errorMessage,
                background: '#f7fafc',
                confirmButtonColor: '#000000ff'
            });
        }
    };

    // Manejo de formularios de eliminación
    const setupDeleteForms = () => {
        const deleteForms = document.querySelectorAll('.products-delete-form');

        deleteForms.forEach(form => {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¿Quieres eliminar este producto? Esta acción no se puede deshacer.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#000000ff',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    background: '#f7fafc',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Mostrar loading
                        Swal.fire({
                            title: 'Eliminando...',
                            text: 'Por favor espere',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        form.submit();
                    }
                });
            });
        });
    };

    // Validación del campo de salto de página
    const setupPageInputValidation = () => {
        const pageInputs = document.querySelectorAll('.products-page-input');
        
        pageInputs.forEach(input => {
            input.addEventListener('change', function() {
                const maxPage = parseInt(this.getAttribute('max')) || 1;
                const minPage = parseInt(this.getAttribute('min')) || 1;
                let value = parseInt(this.value) || 1;
                
                if (value > maxPage) {
                    value = maxPage;
                } else if (value < minPage) {
                    value = minPage;
                }
                
                this.value = value;
            });

            // Validar en tiempo real
            input.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        });
    };

    // Toggle para mostrar/ocultar detalles
    const setupDetailsToggle = () => {
        document.querySelectorAll('.products-toggle-details').forEach(element => {
            element.addEventListener('click', function() {
                const productId = this.getAttribute('data-id');
                const detailsRow = document.getElementById('products-details-' + productId);
                const icon = this.querySelector('i');
                
                if (detailsRow.classList.contains('show')) {
                    detailsRow.classList.remove('show');
                    icon.classList.remove('fa-chevron-up');
                    icon.classList.add('fa-chevron-down');
                    this.setAttribute('aria-expanded', 'false');
                } else {
                    // Cerrar otros detalles abiertos
                    document.querySelectorAll('.products-expandable-row.show').forEach(row => {
                        if (row.id !== 'products-details-' + productId) {
                            row.classList.remove('show');
                            const otherIcon = document.querySelector(`[data-id="${row.id.split('-')[2]}"] i`);
                            if (otherIcon) {
                                otherIcon.classList.remove('fa-chevron-up');
                                otherIcon.classList.add('fa-chevron-down');
                            }
                        }
                    });
                    
                    detailsRow.classList.add('show');
                    icon.classList.remove('fa-chevron-down');
                    icon.classList.add('fa-chevron-up');
                    this.setAttribute('aria-expanded', 'true');
                }
            });

            // Soporte para teclado
            element.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.click();
                }
            });
        });
    };

    // Función para manejar la descarga de PDF con filtros
    const setupPdfDownload = () => {
        const pdfButtons = document.querySelectorAll('a[href*="download=pdf"], a[href*="download=excel"]');
        
        pdfButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                // Obtener los parámetros actuales de la URL
                const currentUrl = new URL(window.location.href);
                const currentParams = new URLSearchParams(currentUrl.search);
                
                // Obtener la URL del botón
                const buttonUrl = new URL(this.href);
                const buttonParams = new URLSearchParams(buttonUrl.search);
                
                // Combinar parámetros (mantener query y page si existen)
                if (currentParams.has('query')) {
                    buttonParams.set('query', currentParams.get('query'));
                }
                if (currentParams.has('page')) {
                    buttonParams.set('page', currentParams.get('page'));
                }
                
                // Actualizar la URL del botón
                buttonUrl.search = buttonParams.toString();
                this.href = buttonUrl.toString();
            });
        });
    };

    // Manejo de la barra lateral responsive
    const setupResponsiveSidebar = () => {
        const sidebar = document.querySelector('.products-sidebar');
        const sidebarToggle = document.querySelector('.products-sidebar-toggle');
        const sidebarOverlay = document.querySelector('.products-sidebar-overlay');
        
        // Crear elementos si no existen
        if (!sidebarToggle && sidebar) {
            const toggleBtn = document.createElement('button');
            toggleBtn.className = 'products-sidebar-toggle';
            toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
            toggleBtn.setAttribute('aria-label', 'Abrir menú de filtros');
            toggleBtn.setAttribute('aria-expanded', 'false');
            document.body.appendChild(toggleBtn);
        }

        if (!sidebarOverlay && sidebar) {
            const overlay = document.createElement('div');
            overlay.className = 'products-sidebar-overlay';
            document.body.appendChild(overlay);
        }

        // Función para abrir la barra lateral
        const openSidebar = () => {
            if (sidebar) {
                sidebar.classList.add('mobile-open');
                document.querySelector('.products-sidebar-overlay').classList.add('active');
                document.querySelector('.products-sidebar-toggle').setAttribute('aria-expanded', 'true');
                document.body.classList.add('sidebar-open');
            }
        };

        // Función para cerrar la barra lateral
        const closeSidebar = () => {
            if (sidebar) {
                sidebar.classList.remove('mobile-open');
                document.querySelector('.products-sidebar-overlay').classList.remove('active');
                document.querySelector('.products-sidebar-toggle').setAttribute('aria-expanded', 'false');
                document.body.classList.remove('sidebar-open');
            }
        };

        // Event listeners
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', openSidebar);
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', closeSidebar);
        }

        // Cerrar sidebar al hacer clic en un enlace dentro de ella
        if (sidebar) {
            sidebar.addEventListener('click', function(e) {
                if (e.target.tagName === 'A' || e.target.closest('a')) {
                    setTimeout(closeSidebar, 300);
                }
            });
        }

        // Cerrar sidebar con la tecla Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSidebar();
            }
        });

        // Ajustar en resize de ventana
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                closeSidebar();
            }
        });
    };

    // Mejoras de accesibilidad para la tabla
    const setupTableAccessibility = () => {
        const table = document.querySelector('.products-table');
        
        if (table) {
            // Agregar roles ARIA
            table.setAttribute('role', 'grid');
            table.querySelector('thead').setAttribute('role', 'rowgroup');
            table.querySelector('tbody').setAttribute('role', 'rowgroup');
            
            // Agregar labels a las celdas
            const headers = table.querySelectorAll('th');
            const rows = table.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                cells.forEach((cell, index) => {
                    if (headers[index]) {
                        cell.setAttribute('aria-label', headers[index].textContent);
                    }
                });
            });
        }
    };

    // Busqueda en tiempo real (opcional)
    const setupRealTimeSearch = () => {
        const searchInput = document.querySelector('.products-form-control[type="search"]');
        const searchForm = document.querySelector('form[method="GET"]');
        
        if (searchInput && searchForm) {
            let searchTimeout;
            
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                
                // Buscar después de 500ms de inactividad
                searchTimeout = setTimeout(() => {
                    if (this.value.length >= 2 || this.value.length === 0) {
                        searchForm.submit();
                    }
                }, 500);
            });
        }
    };

    // Inicialización de todas las funcionalidades
    const initProducts = () => {
        setupSweetAlerts();
        setupDeleteForms();
        setupPageInputValidation();
        setupDetailsToggle();
        setupPdfDownload();
        setupResponsiveSidebar();
        setupTableAccessibility();
        setupRealTimeSearch();
        
        console.log('Products module initialized successfully');
    };

    // Inicializar cuando el DOM esté listo
    initProducts();

    // Re-inicializar después de navegación AJAX (si es necesario)
    if (typeof Livewire !== 'undefined') {
        document.addEventListener('livewire:load', initProducts);
    }
});

// Exportar funciones para uso externo (si es necesario)
window.ProductsModule = {
    refresh: function() {
        window.location.reload();
    },
    closeSidebar: function() {
        const sidebar = document.querySelector('.products-sidebar');
        if (sidebar) {
            sidebar.classList.remove('mobile-open');
            document.querySelector('.products-sidebar-overlay').classList.remove('active');
            document.body.classList.remove('sidebar-open');
        }
    }
};