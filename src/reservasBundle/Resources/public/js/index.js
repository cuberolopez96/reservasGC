(function(){
  let Utils;




   Correos: {
     add: function(nombre,apellidos,correo){
       Utils.postAjax('/api/correos/add',{
         nombre:nombre,
         apellidos:apellidos,
         correos: correo
       }){

       },
       edit: function(id,nombre,apellidos,correos){
         Utils.postAjax('/api/correos/edit',{
           id: id,
           nombre: nombre,
           apellidos: apellidos,
           correo: correo
         });
       }
     }
   }
   Plantillas: {
     add: function(asunto,texto){
       Utils.postAjax('/api/plantillas/add',{
         asunto:asunto,
         texto: texto
       })
     },
     edit: function(id,asunto,texto){
       Utils.postAjax('/api/plantillas/edit',{
         id: id,
         asunto: asunto,
         descripcion: descripcion
       });

     }
   };
   Menu = {
     add: function(descripcion,imagen){
       Utils.postAjax('/api/menu/add',{
         Descripcion:descripcion,
         Imagen: imagen
       });
     },
     edit: function(id,descripcion,imagen){
       Utils.postAjax('/api/menu/edit',{
         id: id,
         descripcion: descripcion,
         imagen: imagen
       })
     }

   };
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
   };
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
 };
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
