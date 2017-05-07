(function(){
  let Utils;


   Menu = {
     add: function(descripcion,imagen){
       Utils.postAjax('/api/menu/add',{
         Descripcion:descripcion,
         imagen: imagen
       })
     }
     
   }
   Servicios= {
     add: function(fechaServicio,plazas){
       Utils.postAjax('/api/servicios/add',{
         FechaServicio: fechaServicio,
         Plazas: plazas
       });
     },
     edit: function(id,fechaServicio,plazas){
       Utils.postAjax('/api/servicios/edit',{
         id:id,
         fecha: fechaServicio,
         Plazas: plazas
       })
     }
   }
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
  Utils.postAjax('/api/menu/edit',{
    id: 1,
    descripcion: 'edicion',
    imagen: ' imagen editada',

  });
  Utils.getAjax('/api/menu',function(data){
    data.forEach(function(row){
      console.log(row);
    })
  });
}
)()
