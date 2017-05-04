(function(){
  let Utils;


   Utils = {

    getAjax: function(url,success=false) {
     let ajax;
     ajax = $.ajax({
       url:url,



     }).done(success);
   },
   postAjax: function(url,data,success=false){
     let ajax
     ajax = $.ajax({
       url:url,
       data:data,
       method: 'POST'
     }).done(function(data){
       console.log(data);
     });
   }
  }
  Utils.getAjax('/api/menu',function(data){
    data.forEach(function(row){
      console.log(row);
    })
  });
  Utils.postAjax('/api/menu/add',{
    Descripcion: 'viene de ajax',
    Imagen: 'aaaa'
  });
}
)()
