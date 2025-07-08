@extends('tenant.layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <div>
                        <h4 class="card-title">Datos DIGEMID Ayuda</h4>
                    </div>
                    <div class="card-actions">
                        <a href="{{ route('tenant.digemid.ayuda.import') }}" class="btn btn-primary">
                            <i class="fas fa-file-import"></i> Importar
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <input type="text" id="search" class="form-control" placeholder="Buscar..."
                            autocomplete="off">
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped mt-2">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Producto</th>
                                    <th>Concentración</th>
                                    <th>Forma Farm.</th>
                                    <th>Presentación</th>
                                    <th>Titular</th>
                                    <th>Fabricante</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody id="data-table-body"></tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <div id="pagination-container"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let currentPage = 1;
        let totalPages = 0;

        $(document).ready(function() {
            loadData();

            $('#search').on('keyup', function() {
                currentPage = 1;
                loadData();
            });
        });

        function loadData() {
            let searchTerm = $('#search').val();

            $.ajax({
                url: '{{ route('tenant.digemid.ayuda.list') }}',
                type: 'GET',
                data: {
                    search: searchTerm,
                    page: currentPage,
                    limit: 15
                },
                success: function(response) {
                    if (response.success) {
                        renderTable(response.data.data);
                        renderPagination(response.data);
                    } else {
                        toastr.error('Error al cargar los datos');
                    }
                },
                error: function() {
                    toastr.error('Error al cargar los datos');
                }
            });
        }

        function renderTable(data) {
            let html = '';

            if (data.length === 0) {
                html = '<tr><td colspan="8" class="text-center">No se encontraron registros</td></tr>';
            } else {
                data.forEach(function(item) {
                    let statusClass = item.active ? 'text-success' : 'text-danger';
                    let statusText = item.active ? 'Activo' : 'Inactivo';

                    html += `
                    <tr>
                        <td>${item.cod_prod || '-'}</td>
                        <td>${item.nom_prod || '-'}</td>
                        <td>${item.concent || '-'}</td>
                        <td>${item.nom_form_farm || '-'}</td>
                        <td>${item.presentac || '-'}</td>
                        <td>${item.nom_titular || '-'}</td>
                        <td>${item.nom_fabricante || '-'}</td>
                        <td class="${statusClass}">${statusText}</td>
                    </tr>
                `;
                });
            }

            $('#data-table-body').html(html);
        }

        function renderPagination(data) {
            let html = '';
            totalPages = data.last_page;

            if (totalPages > 1) {
                html += '<ul class="pagination">';

                // Previous button
                html += `
                <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="changePage(${currentPage - 1}); return false;">Anterior</a>
                </li>
            `;

                // Page numbers
                for (let i = 1; i <= totalPages; i++) {
                    if (i === currentPage) {
                        html += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
                    } else {
                        html +=
                            `<li class="page-item"><a class="page-link" href="#" onclick="changePage(${i}); return false;">${i}</a></li>`;
                    }
                }

                // Next button
                html += `
                <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="changePage(${currentPage + 1}); return false;">Siguiente</a>
                </li>
            `;

                html += '</ul>';
            }

            $('#pagination-container').html(html);
        }

        function changePage(page) {
            if (page >= 1 && page <= totalPages) {
                currentPage = page;
                loadData();
            }
        }
    </script>
@endpush
