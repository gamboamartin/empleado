const registro_id = getParameterByName('registro_id');

const columns_tipos_documentos = [
    {
        title: "Tipo documento",
        data: "doc_tipo_documento_descripcion"
    },{
        title: "Etapa",
        data: "doc_etapa"
    },
    {
        title: "Descarga",
        data: "descarga"
    },
    {
        title: "Vista previa",
        data: "vista_previa"
    },
    {
        title: "ZIP",
        data: "descarga_zip"
    },
    {
        title: "Elimina",
        data: "elimina_bd"
    }
];

const options = {paging: false, info: false, searching: false}

const table_tipos_documentos = table('em_empleado', columns_tipos_documentos, [], [], function () {
    }, true, "tipos_documentos", {registro_id: registro_id}, options);

var modal = document.getElementById("myModal");
var modalSend = document.getElementById("modalSnd");
var closeBtn = document.getElementById("closeModalBtn");
var closeMdl = document.getElementById("closeModalSendBtn");

$(document).on("click", "#table-em_empleado a[title='Vista Previa']", function (event) {
    event.preventDefault();
    var url = $(this).attr("href");

    var loaderOverlay = $('<div class="loader-overlay"><div class="loader"></div></div>');
    $('body').append(loaderOverlay);

    $.ajax({
        url: url,
        type: 'GET',
        success: function (data) {
            var tempDiv = $("<div>").html(data);
            var viewContent = tempDiv.find(".view");

            console.log(data)

            $("#myModal .content").html(viewContent);
            modal.showModal();
            loaderOverlay.remove();
        },
        error: function () {
            $("#myModal .content").html("<p>Error al cargar el contenido.</p>");
            modal.showModal();
            loaderOverlay.remove();
        }
    });
});

closeBtn.onclick = function () {
    $("#myModal .content").empty();
    modal.close();
}

modal.addEventListener('click', function (event) {
    if (event.target === modal) {
        $("#myModal .content").empty();
        modal.close();
    }
});