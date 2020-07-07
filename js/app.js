var imgRecomendado= '<img width="134" src="assets/img/best-seller.png" alt="best seller" class="best-seller">';
$(".card.recomendado").prepend(imgRecomendado);

$(".btn-signed").click(function(event){
  event.preventDefault();
  var button = $(event.currentTarget) // Botão que acionou o modal
  var tipo_plano = button.data('tipo_plano') // Extrai informação dos atributos data-*
  $("#tipo_plano").val(tipo_plano);
  $('#modalPlano').modal('show');
});

$(".sendPlanoBackend").click(function(event){
  event.preventDefault();
  var dados= $("#formPlano").serialize();
  $.ajax({
    method:"POST",
    url:"backend/contact.php",
    data:dados,
    success: function (resposta)
    {
      console.log("sucesso");
      console.log(resposta);
      $('#success').html("<div class='alert alert-success'>");
      $('#success > .alert-success').html("<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;")
        .append("</button>");
      $('#success > .alert-success')
        .append("<strong>" + resposta + " </strong>");
      $('#success > .alert-success')
        .append('</div>');
      $('#formPlano').trigger("reset");
    },
    error:function(resposta){
      console.log("Algum Erro");
      console.log(resposta);
      console.log("status code: ", resposta.status);
      console.log("resposta servidor: ", resposta.responseJSON);
      // Fail message
      $('#success').html("<div class='alert alert-danger'>");
                $('#success > .alert-danger').html("<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;")
                    .append("</button>");
                var list='<ul>';
                  resposta.responseJSON.forEach(function(msg){
                    list+="<li>"+msg+"</li>";
                  });
                list+="</ul>";
                $('#success > .alert-danger').append(list);
                $('#success > .alert-danger').append('</div>');
    },
    beforeSend:function(){
      $('#loading').show();
    },
    complete:function(){
     
      $('#loading').hide();
      var element = document.getElementById("success");
      element.scrollIntoView();
    }
  })
});

$('#modalPlano').on('hidden.bs.modal', function (e) {
  $('#formPlano').trigger("reset");
  $('#success').html('');
})