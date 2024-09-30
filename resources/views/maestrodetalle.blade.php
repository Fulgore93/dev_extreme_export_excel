@extends('layout')
@section('content')
    <div id="container"></div>
@endsection
@section('js')
<script>
    $(document).ready(async function(e) {
        window.jsPDF = window.jspdf.jsPDF;
        const url_list = '{{ route('list') }}';
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
                                let offset = 0;
                                // estilo y color del borde de la tabla del detalle
                                const borderStyle = { style: "thin", color: { argb: "FF7E7E7E" } };

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

                                // por cada fila en el maestro
                                for(var i = 0; i < masterRows.length; i++) {
                                    let columnIndex = cellRange.from.column + 1;
                                    // se declara las columnas que tendrá el detalle
                                    const columns = ["id", "accion", "descripcion", "fecha"];
                                    // se busca nuestro maestro específico dentro del data
                                    let rowDatagrid = data.find((item) => item.id === masterRows[i].data.id);

                                    // recomiendo hacer un console log a rowDataGrid para verificar de que se obtienen correctamente 
                                    // los datos de origen de la fila y su detalle aunque sea vacío
                                    // console.log(rowDatagrid);

                                    // Inicio fila opcional que sirve como título para el detalle
                                    // // se inserta la primera fila título
                                    // let row = insertRow(masterRows[i].rowIndex + i, offset++, 2);
                                    // row.height = 30;
                                    
                                    // // titulo y configuraciones en la casilla
                                    // Object.assign(row.getCell(columnIndex), {
                                    //     value: 'Acciones en '+rowDatagrid.nombre,
                                    //     // documentacion para el fill https://github.com/exceljs/exceljs?tab=readme-ov-file#fills
                                    //     // documentacion para el font https://github.com/exceljs/exceljs?tab=readme-ov-file#fonts
                                    //     fill: { 
                                    //         type: 'pattern', 
                                    //         pattern:'solid', 
                                    //         fgColor: { argb: 'ffffff' },
                                    //         font: { bold: true },
                                    //     }
                                    // });
                                    // // juntar las celdas en una (numero fila, columna inicial, numero fila, numero de columnas hacia la derecha)
                                    // worksheet.mergeCells(row.number, columnIndex, row.number, 6);
                                    // Fin fila opcional que sirve como título para el detalle

                                    // insertar una nueva fila para los headers de las columnas
                                    row = insertRow(masterRows[i].rowIndex + i, offset++, 2);
                                    columns.forEach((columnName, currentColumnIndex) => {
                                        // configuraciones de las celdas en excel
                                        // documentacion para el borders https://github.com/exceljs/exceljs?tab=readme-ov-file#borders
                                        Object.assign(row.getCell(columnIndex + currentColumnIndex), {
                                            value: columnName,
                                            fill: { type: 'pattern', pattern:'solid', fgColor: { argb: 'ffffff' } },
                                            font: { bold: true },
                                            border: { bottom: borderStyle, left: borderStyle, right: borderStyle, top: borderStyle }
                                        });
                                    });
                                    
                                    // por cada detalle que tenga el maestro se insertará una fila 
                                    // ojo con el nombre para el conjunto de detalle en el maestro
                                    // también si los datos deben ser transformados como por ej fechas, monedas, números, etc..
                                    // lo mejor sería que vinieran listos desde el back, ya que puede ser complicado transformarlos aquí, pero posible xd
                                    rowDatagrid.detalle.forEach((task, index) => {
                                        row = insertRow(masterRows[i].rowIndex + i, offset++, 2);
                                        columns.forEach((columnName, currentColumnIndex) => {
                                            if (columnName == 'accion') {
                                                Object.assign(row.getCell(columnIndex + currentColumnIndex), {
                                                    // ojo con los nombres de las columnas que deben ser iguales entre el dato y lo que se definió en columnas
                                                    // en este caso se está enviando directamente un objeto para accion, por lo que hay que rescatar el nombre
                                                    value: task[columnName].nombre,
                                                    fill: { type: 'pattern', pattern:'solid', fgColor: { argb: 'ffffff' } },
                                                    border: { bottom: borderStyle, left: borderStyle, right: borderStyle,top: borderStyle }
                                                });
                                            } else {
                                                Object.assign(row.getCell(columnIndex + currentColumnIndex), {
                                                    // ojo con los nombres de las columnas que deben ser iguales entre el dato y lo que se definió en columnas
                                                    value: task[columnName],
                                                    fill: { type: 'pattern', pattern:'solid', fgColor: { argb: 'ffffff' } },
                                                    border: { bottom: borderStyle, left: borderStyle, right: borderStyle,top: borderStyle }
                                                });
                                            }
                                        });
                                    });
                                    offset--;
                                }
                            }).then(function() {
                                workbook.xlsx.writeBuffer().then(function(buffer) {
                                    saveAs(new Blob([buffer], { type: 'application/octet-stream' }), 'planilla_excel_nombre_nose.xlsx');
                                });
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
                        const currenData = options.data.detalle; // le damos la información de la fila
                        return $('<div>').dxDataGrid({
                            dataSource: currenData,
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
