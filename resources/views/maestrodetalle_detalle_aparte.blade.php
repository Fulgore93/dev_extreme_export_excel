@extends('layout')
@section('content')
    <div id="container"></div>
@endsection
@section('js')
<script>
    $(document).ready(async function(e) {
        window.jsPDF = window.jspdf.jsPDF;
        const url_list = '{{ route('maestro') }}';
        var items = new DevExpress.data.CustomStore({ // función para el origen de datos
            key: "id",
            load: function() {
                return sendRequest(url_list, "GET");
            }
        });  

        DevExpress.localization.locale(navigator.language);
        
        $(function() {
            const dataGrid = $('#container').dxDataGrid({ 
                dataSource: items,
                columnAutoWidth: true,
                showBorders: true, // mostrar bordes de la tabla
                hoverStateEnabled: true, // color en la fila al pasar el mouse por encima
                columnHidingEnabled: true, // ocultar columnas si no alcanzan a desplegarse en la resolucion
                allowColumnReordering: true, // permite mover las columnas (cambiar de orden) al actualizar vuelve a la normalidad
                // rowAlternationEnabled: true, // fila de color intercalada
                wordWrapEnabled: true, // permite visualizar todo el texto en una columna (pasa la siguiente, como si hiciera enter)
                searchPanel: { // 1 panel para buscar palabras
                    visible: true,
                    width: 240,
                    placeholder: 'Buscar...',
                },
                headerFilter: { // filtro para filtrar al seleccionar valores de la columna en la cabecera
                    visible: true,
                },
                filterRow: { //lupita para buscar en columna
                    visible: true,
                    applyFilter: 'auto', // puede ser auto u onClick
                    betweenStartText: 'Inicio',
                    betweenEndText: 'Fin',
                },
                pager: { // paginador, cuantas filas se muestran
                    allowedPageSizes: [10, 25, 50, 100],
                    showInfo: true,
                    showNavigationButtons: true,
                    showPageSizeSelector: true,
                    visible: 'true',
                },
                paging: { // numero de filas a mostrar
                    pageSize: 10,
                },
                groupPanel: { // boton en tooltip
                    visible: true,
                },
                grouping: { // expandir automaticamente si es que viene algo ya agrupado
                    autoExpandAll: true,
                },
                columnChooser: { // escoger que columnas se muestran u ocultar al presionar un botón y seleccionar
                    enabled: true,
                    mode: 'select',
                },
                export: {
                    enabled: true,
                    formats: ['xlsx', 'pdf']
                },
                onExporting: function(e) {
                    // primero necesitamos que se hayan cargado los datos
                    items.load().done(function(data) {
                        if (e.format === 'xlsx') {
                            var workbook = new ExcelJS.Workbook();
                            var worksheet = workbook.addWorksheet('Listado de Items');

                            // array vacío para las filas
                            let masterRows = [];

                            DevExpress.excelExporter.exportDataGrid({
                                component: e.component,
                                worksheet: worksheet,
                                topLeftCell:  { row: 2, column: 2 },
                                customizeCell: function({ gridCell, excelCell }) {
                                    // indicar la columna desde la que comenzará el detalle (será la siguiente)
                                    if(gridCell.column.dataField === 'id' && gridCell.rowType === 'data') {
                                        // se pushea el índice desde donde comenzará y los datos de la fila que están en data
                                        masterRows.push({ rowIndex: excelCell.fullAddress.row + 1, data: gridCell.data });
                                    }
                                }
                            }).then((cellRange) => {
                                
        var dataGridInstance = e.component;
        var firstRowData = dataGridInstance.getVisibleRows()[0];

        if (firstRowData && firstRowData.isExpanded) {
            // Si la primera fila está expandida, accede al detalle
            var detailGridInstance = firstRowData.data.detailsInstance; // Aquí se guarda la instancia del detalle

            // Si existe el detalle, imprime los datos en la consola
            if (detailGridInstance) {
                console.log(detailGridInstance.option('dataSource').items());
            } else {
                console.log("El detalle de la primera fila no está disponible.");
            }
        } else {
            console.log("La primera fila no está expandida o no tiene detalle.");
        }
                                let offset = 0;
                                
                                // función para insertar una fila en el excel
                                const insertRow = (index, offset, outlineLevel) => {
                                    const currentIndex = index + offset;
                                    const row = worksheet.insertRow(currentIndex, [], 'n');
                                    
                                    for(var j = worksheet.rowCount + 1; j > currentIndex; j--) {
                                        worksheet.getRow(j).outlineLevel = worksheet.getRow(j - 1).outlineLevel;
                                    }

                                    row.outlineLevel = outlineLevel;

                                    return row;
                                }
                                // estilo y color del borde de la tabla del detalle
                                const borderStyle = { style: "thin", color: { argb: "FF7E7E7E" } };
                                
                                // Por cada fila en el maestro
                                masterRows.forEach((masterRow) => {
                                    let rowDatagrid = masterRow.data;

                                    // Acceder a la fila expandida en el DataGrid para obtener los detalles
                                    let rowIndex = e.component.getRowIndexByKey(rowDatagrid.id);
                                    let rowElement = e.component.getRowElement(rowIndex);

                                    let detallesCargados = [];

                                    // Verificar si la fila tiene detalles expandidos
                                    if (rowElement && rowElement.length > 0) {
                                        let detailRows = $(rowElement).next('.dx-master-detail-row').find('.dx-datagrid-table tbody tr');
                                        console.log(detailRows);
                                        
                                        detailRows.each(function() {
                                            let detailData = $(this).data('options').data;
                                            detallesCargados.push(detailData);
                                        });
                                    }

                                    console.log(detallesCargados);
                                    

                                    // Si hay detalles, los agregamos al Excel
                                    if (detallesCargados.length > 0) {
                                        let columnIndex = cellRange.from.column + 1;
                                        const columns = ["id", "accion", "descripcion", "fecha"];

                                        // Insertar una nueva fila para los headers de las columnas del detalle
                                        let row = insertRow(masterRow.rowIndex + offset, 2);
                                        columns.forEach((columnName, currentColumnIndex) => {
                                            Object.assign(row.getCell(columnIndex + currentColumnIndex), {
                                                value: columnName,
                                                fill: { type: 'pattern', pattern:'solid', fgColor: { argb: 'ffffff' } },
                                                font: { bold: true },
                                                border: { bottom: borderStyle, left: borderStyle, right: borderStyle, top: borderStyle }
                                            });
                                        });
                                        offset++;

                                        // Insertar filas del detalle
                                        detallesCargados.forEach((task) => {
                                            row = insertRow(masterRow.rowIndex + offset, 2);
                                            columns.forEach((columnName, currentColumnIndex) => {
                                                let cellValue = columnName === 'accion' ? task[columnName].nombre : task[columnName];
                                                Object.assign(row.getCell(columnIndex + currentColumnIndex), {
                                                    value: cellValue,
                                                    fill: { type: 'pattern', pattern:'solid', fgColor: { argb: 'ffffff' } },
                                                    border: { bottom: borderStyle, left: borderStyle, right: borderStyle, top: borderStyle }
                                                });
                                            });
                                            offset++;
                                        });
                                    }
                                });

                            }).then(function() {
                                // workbook.xlsx.writeBuffer().then(function(buffer) {
                                //     saveAs(new Blob([buffer], { type: 'application/octet-stream' }), 'planilla_excel_nombre_nose.xlsx');
                                // });
                            });
                            e.cancel = true;
                        }else if (e.format === 'pdf') {
                            // hasta el momento, no es posible imprimir el maestro junto con el detalle como en excel
                            // al probar algunas cosas, se lograba imprimir el detalle en una tabla, pero se solapaban
                            const doc = new jsPDF();
                            DevExpress.pdfExporter.exportDataGrid({
                                jsPDFDocument: doc,
                                component: e.component,
                            }).then(() => {
                                doc.save('datos_pdf.pdf');
                            });
                        }else{
                            console.log('ni xlsx ni pdf');
                        }
                    });
                },
                masterDetail: {
                    enabled: true,
                    template(container, options) {
                        const currenData = options.data; // le damos la información de la fila
                        return $('<div>').dxDataGrid({
                            dataSource: new DevExpress.data.DataSource({
                                key: "id",
                                load: function() {  
                                    var url_detalle = '{{ route('detalle', ':id') }}'.replace(':id', currenData.id);
                                    return sendRequest(url_detalle, "GET");
                                }, 
                            }),
                            columnAutoWidth: true,
                            showBorders: true,
                            columns:  [
                                {
                                    dataField: 'id',
                                    caption: 'Id',
                                    format : "#,##0.###", 
                                    hidingPriority: 3, // prioridad para ocultar columna, 0 se oculta primero
                                }, 
                                {
                                    dataField: 'accion.nombre',
                                    caption: 'accion',
                                    hidingPriority: 2, // prioridad para ocultar columna, 0 se oculta primero
                                }, 
                                {
                                    dataField: 'descripcion',
                                    caption: 'Descripción',
                                    hidingPriority: 1, // prioridad para ocultar columna, 0 se oculta primero
                                },
                                { 
                                    dataField: 'created_at',
                                    caption: 'Fecha de creación',
                                    dataType: 'date',
                                    format: "dd-MM-yyyy",
                                    hidingPriority: 0, // prioridad para ocultar columna, 0 se oculta primero
                                },
                            ],
                            paging: { // numero de filas a mostrar
                                pageSize: 10,
                            },
                        });
                    },
                },
                columns: [
                    // filtro en cabecera para NUMERIC filterOperations:[ "=", "<>", "<", ">", "<=", ">=", "between" ],
                    // filtro en cabecera para STRING filterOperations:[ "contains", "notcontains", "startswith", "endswith", "=", "<>" ],
                    // filtro en cabecera para DATE filterOperations:[ "=", "<>", "<", ">", "<=", ">=", "between" ],
                    // en caso de tener 2 o más filtros, para dejar uno por defecto se usa selectedFilterOperation: "between",
                    {
                        dataField: 'id',
                        caption: 'Id',
                        filterOperations: ["contains"],
                        format : "#,##0.###", 
                        filterOperations: [ "=", "<>", "<", ">", "<=", ">=", "between" ],
                        // allowExporting: true,
                        hidingPriority: 7, // prioridad para ocultar columna, 0 se oculta primero
                    }, 
                    {
                        dataField: 'nombre',
                        caption: 'Nombre',
                        filterOperations: ["contains"],
                        // allowExporting: true,
                        hidingPriority: 5, // prioridad para ocultar columna, 0 se oculta primero
                    }, 
                    {
                        dataField: 'descripcion',
                        caption: 'Descripción',
                        filterOperations: ["contains"],
                        // allowExporting: true,
                        hidingPriority: 4, // prioridad para ocultar columna, 0 se oculta primero
                    },
                    { 
                        dataField: 'created_at',
                        caption: 'Fecha de creación',
                        dataType: 'date',
                        format: "dd-MM-yyyy",
                        filterOperations: ["between"],
                        selectedFilterOperation: "between",
                        filterOperations: ["between"],
                        hidingPriority: 3, // prioridad para ocultar columna, 0 se oculta primero
                    },
                    {
                        dataField: 'precio',
                        caption: 'Monto',
                        format : "#,##0.###", 
                        filterOperations: [ "=", "<>", "<", ">", "<=", ">=", "between" ],
                        hidingPriority: 2, // prioridad para ocultar columna, 0 se oculta primero
                    }, 
                    {
                        dataField: 'stock',
                        caption: 'Stock',
                        format : "#,##0.###", 
                        filterOperations: [ "=", "<>", "<", ">", "<=", ">=", "between" ],
                        hidingPriority: 1, // prioridad para ocultar columna, 0 se oculta primero
                    }, 
                    {
                        dataField: 'estado.nombre',
                        caption: 'Estado',
                        filterOperations: ["contains"],
                        // allowExporting: true,
                        hidingPriority: 0, // prioridad para ocultar columna, 0 se oculta primero
                    }, 
                    {    
                        dataField: '',
                        caption: 'Opciones',
                        alignment: 'center',
                        allowExporting: false,
                        hidingPriority: 6, // prioridad para ocultar columna, 0 se oculta primero
                        cellTemplate(container, options) {
                            const icon_edit = "<a class='' href='#'>Editar</a> ";
                            const icon_delete = " <a rel='"+options.data.id+"'href='#'> Eliminar</a>";
    
                            return $('<div>').append(icon_edit).append(icon_delete);
                        },
                    },

                ],
            }).dxDataGrid('instance');
        });
    });
</script> 
@endsection
