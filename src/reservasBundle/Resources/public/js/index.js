//(function(){
  let Utils,Calendar,Servicios,serviciosdata,fechaActual;

  $(document).ready(function(){
    // Si estamos en consultar
    if (window.location.pathname === '/consultar') {

      $('#buscar').click(function(){
        let busqueda = $('#entrada').val();
        Reservas.getReservasByCodReservas(busqueda,function(data){
          $('#resultadoConsulta').empty().append('<div id="busqueda" class="card">'+
            '<div class="card-content">'+
            '<span class="card-title">'+ data.Nombre + ' ' + data.Apellidos +'</span>'+
            '<p class="col s6">Fecha Servicio: '+ data.Servicio.FechaServicio +'</p> '+
            '<p class="col s6">Correo: '+ data.Correo + '</p>'+
            '</div>'+
            '<div class="card-action">'+
            '<a id="editarReserva" href="#">Editar</a>'+
            '<a id="anular" href="#modal2">Anular</a>'+
            '</div>'+
          '</div>');

          $("#editarReserva").click(function(){
            let checkbox = $('#check input'),alergenos = "";
            $("#busqueda").css("display", "none");
            $('#name').val(Reservas.consultaCache.Nombre);
            $('#ap').val(Reservas.consultaCache.Apellidos);
            $('#email').val(Reservas.consultaCache.Correo);
            $('#tlfn').val(Reservas.consultaCache.Telefono);
            $('#observaciones').val(Reservas.consultaCache.Observaciones);
            Reservas.getAlergenos(Reservas.consultaCache.Id,function(data){
              alergenos = data;
              $.each(checkbox,function(index,value){
                $.each(alergenos,function(index,row){
                    if (row.alergeno.Nombre === value.getAttribute('id')) {
                        value.checked  = true;
                    }
                });
              });
            });
            console.log(alergenos);



            $("#formularioConsulta").css("display", "block");
          }); //getReservasByCodReservas

          $('#salir').click(function(event){
            event.preventDefault();
            $('#formularioConsulta').css('display','none');
            $('#busqueda').css('display','block');
          });
          $('#anular').click(function(event){
            //event.preventDefault();
            let id=Reservas.consultaCache.Id;
            Reservas.delete(id);
            $('.modal').modal();
            $('#modal2').modal('open');
          })
        }); //buscar

        $("#guardar").click(function(event){
          //event.preventDefault();
          let id = Reservas.consultaCache.Id,
            nombre = $("#name").val(),
            apellidos = $("#ap").val(),
            correo = $("#email").val(),
            telefono = $("#tlfn").val(),
            observaciones = $("#boservaciones").val(),
            checkbox = $("#check input"),
            servicio = Reservas.consultaCache.Servicio.id;
            alergenos = [];

            $.each(checkbox, function(indice, v){
              console.log(v);
              if (v.checked) {
                alergenos.push(v.getAttribute('id'));
              }
            });
            //validar
            if(Reservas.validateInput()){
              Reservas.edit(id,nombre,apellidos,correo,telefono,observaciones,alergenos,servicio);
            }
            //$('.modal').modal();
            //$('#modal1').modal('open');


        });
      }); //si en consultar

    }
    // Si estamos en reservas
    if (window.location.pathname === '/reservas') {
      let fecha,hora,plazas,nombre,apellidos,correo,telefonos,checkbox,observaciones;
      fechaActual = new Date();
      Calendar.mes = new Date().getMonth();
      Calendar.año = new Date().getFullYear();
      Servicios.Get(function(){
        Calendar.renderCalendar(Servicios.ServiciosCache);
      })
      Calendar.ToggleButtons();
      Servicios.getPlazasOcupadas(new Date(2017,04,16,12,0,0));
      //Servicios.add(new Date(),25);
      $('#diaAnterior').click(function(event){
        let date;
        event.preventDefault();
        Calendar.mes = Calendar.mes - 1;
        Calendar.ToggleButtons();
        Calendar.renderCalendar(Servicios.ServiciosCache);

      });
      $('#diaSiguiente').click(function(event){
        event.preventDefault();
        Calendar.mes = Calendar.mes+ 1;
        Calendar.ToggleButtons();
        Calendar.renderCalendar(Servicios.ServiciosCache);
      })
      $('#atras0').click(function(){
        $('#datos1').css('display','none');
        $('#datos0').css('display','block');
      });
        $('#atras1').click(function(){
          $('#datos2').css('display','none');
          $('#datos1').css('display','block');
        });
        $('#siguiente').click(function(){
          $('#datos2').css('display','none');
          fecha = $('#fecha').val();
          hora = $('#hora').val();
          plazas = $('#plazas').val();
          $('#checkbox').children('input');
          $('#datos3').css('display','block');

        });
        $('#atras2').click(function(){
          $('#datos3').css('display','none');
          $('#datos2').css('display','block');
        });
        $('#reservar').click(function(){

          if(Reservas.validateInput() == false){
            console.log('error');
            return
          }
          $('#datos3').css('display','none');
          $('#parte1').removeClass('ubicacion');
          Reservas.nombre = $('#name').val();
          Reservas.apellidos = $('#ap').val();
          Reservas.correo = $('#email').val();
          Reservas.telefono = $('#tlfn').val();
          Reservas.observaciones = $('#observaciones').val();
          Reservas.plazas = $('#plazas').val();
          checkbox = [];
          $.each($('#check input'),function(index,value){
            if(value.checked  == true){
              checkbox.push(value.id);
            }
          });
          console.log(checkbox);
          Reservas.alergenos = checkbox;
          $('#datosparaconfirmar').empty().append('<p class="col s12"> Nombre: '+ Reservas.nombre +'</p>')
          .append('<p class="col s12">Apellidos: '+ Reservas.apellidos + '</p>')
          .append('<p class="col s12">Correo: '+ Reservas.correo + '</p>')
          .append('<p class="col s12">Telefono: '+ Reservas.telefono + '</p>')
          .append('<p class="col s12">Observaciones: '+ Reservas.observaciones + '</p>');
          p = $('<p class="col s12">Alergenos: </p>');
          Reservas.alergenos.forEach(function(value){
            p.text(p.text() +  value + ',');
          });
          $('#datosparaconfirmar').append(p);
          console.log(Reservas + ' Confirmacion');
          $('#parte2').addClass('ubicacion');
          $('#confirmacion').css('display','block');
        });
        $('#atras3').click(function(){
          $('#confirmacion').css('display','none');
          $('#parte2').removeClass('ubicacion');
          $('#parte1').addClass('ubicacion');
          $('#datos3').css('display','block');
        });
        $('#confirmar').click(function(){
          $('#confirmacion').css('display','none');
          Reservas.add(Reservas.nombre,
            Reservas.apellidos,
            Reservas.correo, Reservas.telefono,
            Reservas.observaciones, Reservas.alergenos
            ,Reservas.idServicio,
            Reservas.plazas, function(data){
              $('#descargar').click(function(){
                window.location.pathname = 'pdf/'+data.Id;
              });
            });

          $('#parte2').removeClass('ubicacion');
          $('#parte3').addClass('ubicacion');
          $('#realizado').css('display','block');
        });

        $('#salir').click(function(){
          window.location.pathname= '/';
        })
    }
  }); //ready
//crear el objeto reservas
  Reservas = {
    idServicio:null,
    Nombre: null,
    Apellidos:null,
    Correo: null,
    telefono: null,
    observaciones: null,
    alergenos: null,
    ReservasCache: null,
    consultaCache:null,
    reservaCache: null,
    delete: function(id){
      Utils.postAjax('api/reservas/delete',{
        id:id
      },function(data){
        return data;
      });


    },
    edit: function(id,nombre,apellidos,correo,telefono,observaciones,alergenos,idservicio){
        Utils.postAjax('api/reservas/edit',{
          id:id,
          nombre:nombre,
          apellidos:apellidos,
          correo:correo,
          telefono:telefono,
          alergenos:alergenos,
          observaciones: observaciones,
          idservicio:idservicio

        },function(data){

          return data;
        })
    },
    getReservasByCodReservas(codigo,success=null){
      Utils.postAjax('api/reservas/codreserva',{
        codigo:codigo
      },function(data){
        Reservas.consultaCache = data;
        success(data);
      });
    },
// valida los campos del formulario de reservas
    validateInput: function(){
      let correcto = true;
      $('.errores').css('display','none');
      if(/^[a-zA-Z]+(\s*[a-zA-Z]*)*/.test($('#name').val())===false){
        correcto = false;
        console.log("nombre");
        $('#errorname').fadeIn("slow");

      }
      if(/^[a-zA-Z]+(\s*[a-zA-Z]*)*/.test($('#ap').val())===false){
        correcto = false;
        console.log("apellidos");
        $('#errorap').fadeIn("slow");

        }
      if(/^.+@.+\..+/.test($('#email').val())===false){
        correcto = false;
        console.log("correo");
        $('#erroremail').fadeIn("slow");
      }
      if(/^\d\d\d\d\d\d\d\d\d$/.test($('#tlfn').val())===false){
        correcto = false;
        console.log("telefono");
        $('#errortlfn').fadeIn("slow");

      }
      if($('#robot')[0].checked===false){
        correcto = false;
        console.log("robot");
        $('#errorrobot').fadeIn("slow");

      }
      console.log();
      return correcto;

    },
    //se trae las reservas de el servidor
    Get:function(){
      Utils.getAjax('/api/reservas',function(data){
        Reservas.ReservasCache = data;
      });

    },
    // añade reservas
    add:function(nombre,apellidos,correo,telefono,observaciones,alergenos,servicio,npersonas,success = null){
      Utils.postAjax('api/reservas/add',{
        nombre: nombre,
        apellidos: apellidos,
        telefono: telefono,
        correo: correo,
        observaciones: observaciones,
        servicio: servicio,
        alergenos: alergenos,
        npersonas: 25
      },function(data){
          success(data);
          return data;
      })
    },
    getAlergenos: function(id,success){
      Utils.postAjax('api/reservas/alergenos',{
        id:id
      },function(data){
        Reservas.alergenos = data;
          success(data);
      })

    }


  }
  // Objeto que controla la gestion de los servicios
   Servicios = {
     ServiciosCache: null,//datos de los servicios guardados en cache;
     Get: function(success = null){
       Utils.getAjax('/api/servicios',function(data){
         Servicios.ServiciosCache = data;
         success();
       });
     },
     GetByFecha: function(fecha){
       Utils.postAjax('/api/servicios/fecha',{
         fecha:fecha
       },function(data){
         return data;
       });
     },
     GetFechas: function(){
       let datos = Servicios.ServiciosCache;
       let fechas = [];
       let fecha;
       $.each(datos,function(value){
         fecha = Utils.converToDate(value.FechaServicio);
       })
     },
     add: function(date,plazas){
       Utils.postAjax('api/servicios/add',{
         Plazas:plazas,
         FechaServicio: Utils.datePostformat(date)
       },function(){
         return true
       })
     },
     getPlazasOcupadas: function(date){
       Utils.postAjax('api/reservas/plazas',{
         fecha: Utils.datePostformat(date)
       },function(data){
         console.log(data);
       })
     }
   };
   Calendar = {
     mes: null,
     meses: ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
     //habilita o deshabilita los botones del calendario en función del mes
     ToggleButtons: function(){
       if(Calendar.mes <= fechaActual.getMonth()){
         $('#diaAnterior').attr('disabled','disabled');
       }else{
         $('#diaAnterior').removeAttr('disabled');
       }
       if(Calendar.mes >= 11){
         $('#diaSiguiente').attr('disabled','disabled');
       }else{
         $('#diaSiguiente').removeAttr('disabled');
       }
     },
     //habilitar dias
     EnableDate: function(dia,servicios){
       //Declaramos variables que vayamos a usar
       let paramDate, dateServicio, flag;
       flag = false;
       //Convertimos ese dia en un date
       paramDate=new Date();
       paramDate.setMonth(Calendar.mes);
       paramDate.setDate(dia);
       //Recorremos los servicios
      $.each(servicios,function(index,value){
         // la fecha del Servicio en un date;
         dateServicio = Utils.converToDate(value.FechaServicio);
         // Comparamos date y devolvemos true si es igual
         if (Utils.dateStringFormat(paramDate) === Utils.dateStringFormat(dateServicio)) {
           flag =  true;
         }
       });
       //false si no lo son
       return flag;
     },
     //crear el metodo selectDate;
     SelectDate: function(dia){
       console.log('he entrado');
        let date;
        date = new Date();
        date.setMonth(Calendar.mes);
        date.setDate(dia);
        date.setFullYear(2017);
        Utils.postAjax('api/servicios/fecha',{
          fecha: Utils.dateStringFormat(date)
        },function(data){
          console.log('he entrado 2');
          $('#datos1  .row #servicios').empty();
          data.forEach(function(row){
            console.log(row);
            $('#datos1 .row #servicios').append("<div class='cards'><div class='card white horizontal'>"+
              "<div class='card-image'>"+
                "<img src='bundles/reservas/img/cardImageVerde.jpg'>"+
              "</div>"+
              "<div class='card-stacked'>"+
                "<div class='card-content'>"+
                  "<p><strong>Disponible</strong></p>"+
                  "<p>"+ row.Fecha.date +"</p>"+
                  "<p>"+ row.Plazas +" plazas</p>"+
                "</div>"+
                "<div class='card-action'>"+
                  "<button id='"+row.Id+"' class='button especial next'>Elegir</button>"+
                "</div>"+
              "</div>"+
            "</div></div>");
          });
          switch(data.length){
          case 1:
              $('#datos1 .row #servicios .cards').addClass('col s12');
              break;
          default:
              $('#datos1 .row #servicios .cards').addClass('col s6');
              break;
          }
          $('.next').click(function(){
            Reservas.idServicio = $(this).attr('id');
            console.log($(this).attr('id') + 'id de el servicio');
            $('#datos1').css('display','none');
            $('#datos2').css('display','block');
          });
          return true;
        });
     },
     renderCalendar: function(servicios){
        let max,fecha,fechas, semana;
        fecha = new Date();
        fecha.setDate(1);
        fecha.setMonth(Calendar.mes);
        $('#mesActual').empty().text(Calendar.meses[Calendar.mes]);
        $('#calendar').empty();
        semana = 0;
        switch(Calendar.mes){
          case 0:
          case 2:
          case 4:
          case 6:
          case 7:
          case 9:
          case 11:
              max = 31;
              break;
          case 1:
              max = 28;
              //algoritmo año bisiesto
              break;
          default:
              max = 30;
              break;
        }
        fechas = [];
        auxfechas = [];
        for (var i = 0; i < max; i++) {
          auxfechas[fecha.getDay()] = fecha.getDate();
          console.log(fecha.getDate()+'/'+(fecha.getMonth()+1)+' '+fecha.getDay());
          fecha = new Date(fecha.getTime() + (60 * 60 * 24 * 1000));
          if (fecha.getDay()==1) {

            fechas[semana]=auxfechas;
            auxfechas = [];
            semana++;
          }
        }
        if ($.inArray(auxfechas,fechas) == -1 ) {
          fechas[semana] = auxfechas;
        }
        console.log(max);
        console.log(fechas);
        fechas.forEach(function(value,index){
          let domingo= null;
          tr = $('<tr id = "'+ index +'"></tr>');
          for (var i = 1; i < 7; i++) {
            if (value[i]) {
              if (Calendar.EnableDate(value[i],servicios)===true) {
                tr.append('<td id="'+i+'"><button data-target="modal" class="btn-floating btn-tiny bcalendario" data-activates="slide-out">'+value[i]+'</button></td>');
              }else{
                tr.append('<td id="'+i+'">'+value[i]+'</td>');
              }
            }else{
              tr.append('<td></td>');
            }
          }
          if (value[0]) {
            if (Calendar.EnableDate(value[0],servicios)===true) {
              tr.append('<td id="'+i+'"><button data-target="modal" class="btn-floating btn-tiny bcalendario">'+value[0]+'</button></td>');
            }else{
              tr.append('<td id="'+i+'">'+value[0]+'</td>')
            }
          }
          $('#calendar').append(tr);
        });
        /*$(".bcalendario").mouseenter(function(){
          $('.modal').modal();
          $('#modal').modal('open');
        });*/

        $(".bcalendario").click(function(){
          let dia;
          dia = $(this).text();
          console.log(dia);
          console.log(Calendar);
          Calendar.SelectDate(dia);
          $('#datos0').css('display','none');
          $('#datos1').css('display','block');
        });
     }
   };
   Utils = {
    dateStringFormat:function(date){
      return date.getDate() + '-' + (date.getMonth() + 1) + '-' + date.getFullYear();
    },
    datePostformat(date){
      return date.getDate() + '-' + (date.getMonth() + 1) + '-' + date.getFullYear() + ' ' +
       date.getHours() + ':' + date.getMinutes() + ':' + date.getSeconds();
    },
    converToDate: function(fecha){
      let tiempo,day,month,year;
      let date = new Date();
      fecha  = fecha.split(' ');
      tiempo = fecha[1];
      fecha = fecha[0];
      fecha = fecha.split('/');
      day = fecha[2];
      month = fecha[1];
      year = fecha[0];
      tiempo = tiempo.split(':');
      hour = tiempo[0];
      minutes = tiempo[1];
      seconds = tiempo[2];
      date.setDate(day);
      date.setMonth(month - 1);
      date.setFullYear(year);
      date.setHours(hour);
      date.setMinutes(minutes);
      date.setSeconds(seconds);
      return date;
    },
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
     }).done(success);
   }
 };
//})()
