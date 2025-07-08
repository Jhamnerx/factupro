@extends('tenant.layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <div>
                        <h4 class="card-title">Importar datos DIGEMID Ayuda</h4>
                    </div>
                </div>
                <div class="card-body">
                    <form id="form-import" method="POST" action="{{ route('tenant.digemid.ayuda.process_import') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="file">Archivo Excel:</label>
                            <input type="file" class="form-control" name="file" required accept=".csv, .xls, .xlsx">
                            <small class="form-text text-muted">Seleccione un archivo Excel con los datos de DIGEMID</small>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Importar</button>
                            <a href="{{ route('tenant.digemid.ayuda.index') }}" class="btn btn-danger">Cancelar</a>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <div id="import-result" style="display: none;">
                        <h5>Resultado de la importación:</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td>Total de registros:</td>
                                        <td id="total-records">0</td>
                                    </tr>
                                    <tr>
                                        <td>Registros importados:</td>
                                        <td id="imported-records">0</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#form-import').submit(function(e) {
                e.preventDefault();

                let formData = new FormData(this);

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#total-records').text(response.data.total);
                            $('#imported-records').text(response.data.registered);
                            $('#import-result').show();
                            toastr.success('Importación realizada con éxito');
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        toastr.error('Error al procesar el archivo: ' + error);
                    }
                });
            });
        });
    </script>
@endpush
