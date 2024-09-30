//div contenedor de la devextreme
const container = document.querySelector('#container')

//lenguaje dev extreme
DevExpress.localization.locale(navigator.language)
//carga de datos dev extreme
const url_list = container.getAttribute('data-list')
let items = new DevExpress.data.CustomStore({ // función para el origen de datos
    key: "id",
    load: function() {
        return sendRequest(url_list, "GET");
    }
}); 
//tabla dev extreme
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
    columnChooser: { // escoger que columnas se muestran u ocultar al presionar un botón y seleccionar
        enabled: true,
        mode: 'select',
    },
    // con el toolbar se pueden separar los botones para el exportar, pero se debe ingresar cada opción del toolbar
    // toolbar: {
    //     items: [
    //         {
    //             widget: 'dxButton',
    //             options: {
    //                 icon: 'exportpdf',
    //                 text: 'Exportar a PDF',
    //                 onClick: function() {
    //                     const doc = new jsPDF({
    //                         orientation: 'landscape' // Orientación horizontal
    //                     });
    //                     DevExpress.pdfExporter.exportDataGrid({
    //                         jsPDFDocument: doc,
    //                         component: dataGrid
    //                     }).then(function() {
    //                         doc.save('export_pdf.pdf');
    //                     });
    //                 }
    //             },
    //         },
    //         {
    //             widget: 'dxButton',
    //             options: {
    //                 icon: 'exportxlsx',
    //                 text: 'Exportar a Excel',
    //                 onClick: function(e) {
    //                     var workbook = new ExcelJS.Workbook(); 
    //                     var worksheet = workbook.addWorksheet('Main sheet'); 
    //                     DevExpress.excelExporter.exportDataGrid({ 
    //                     worksheet: worksheet, 
    //                     component: dataGrid,
    //                     customizeCell: function(options) {
    //                         options.excelCell.font = { name: 'Arial', size: 12 };
    //                         options.excelCell.alignment = { horizontal: 'left' };
    //                     } 
    //                     }).then(function() {
    //                     workbook.xlsx.writeBuffer().then(function(buffer) { 
    //                         // nombre del documento a descargar
    //                         saveAs(new Blob([buffer], { type: 'application/octet-stream' }), 'export_excel.xlsx'); 
    //                     }); 
    //                     }); 
    //                 }
    //             },
    //         },
    //         "exportButton",
    //         "searchPanel"  // Mantener el searchPanel en la toolbar
    //     ]
    // },
    // con esta opción aparecerá un boton para exportar, que al presionar, mostrará exportar pdf y exportar excel
    export: {
        enabled: true,
        formats: ['xlsx', 'pdf']
    },
    onExporting(e) {
        if (e.format === 'xlsx') {
            const workbook = new ExcelJS.Workbook(); 
            const worksheet = workbook.addWorksheet("Main sheet"); 
            DevExpress.excelExporter.exportDataGrid({ 
                worksheet: worksheet, 
                component: e.component,
            }).then(function() {
                workbook.xlsx.writeBuffer().then(function(buffer) { 
                        saveAs(new Blob([buffer], { type: "application/octet-stream" }), "datos_excel.xlsx"); 
                }); 
            }); 
            e.cancel = true;
        } 
        else if (e.format === 'pdf') {
            const doc = new jsPDF();
            DevExpress.pdfExporter.exportDataGrid({
                jsPDFDocument: doc,
                component: e.component,
            }).then(() => {
                doc.save('datos_pdf.pdf');
            });
        }
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
            filterOperations:[ "contains" ],
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
