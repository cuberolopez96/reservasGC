//(function(){
  let Utils,Calendar,Servicios,serviciosdata,fechaActual;

  $(document).ready(function(){
    $('.filtro').keyup(function(event){
        console.log('hola');
        let id, filtrados, value,bandera;
        id = $(this).attr('id');
        id = id.replace('input','');
        filtrados = $('.row'+id);
        search = $(this).val();
        filtro = new RegExp(search);
        bandera = false;
        $.each(filtrados,function(index,row){
          value = row.textContent;
          if (filtro.test(value) == false) {
            row.parentNode.setAttribute('style','display:none');
          }else{
            row.parentNode.removeAttribute('style');
            bandera = true;
          }
        });
        if (bandera == false) {
          $('.row'+id).parent().removeAttr('style');
        }
    });
    // Si estamos en admin servicios
    if (window.location.pathname === '/admin/servicios' || window.location.pathname === '/admin/servicios/anteriores') {
      $('.modaldelete').click(function(event){
        let idservicio = $(this).attr('servicio');
        event.preventDefault();
        $('#modal1').modal();
        $('#modal1').modal('open');
        $('#delete').click(function(event){
          event.preventDefault();
          window.location.pathname="/admin/servicios/delete/"+idservicio;
        });
      })
    }

    // Si estamos en consultar
    if (window.location.pathname === '/consultar') {

      $('#buscar').click(function(){
        let busqueda = $('#entrada').val();
        Reservas.getReservasByCodReservas(busqueda,function(data){
          let stralergenos = [];
          Reservas.getAlergenos(data.Id,function(alergenos){
              $.each(alergenos,function(index, row){
                stralergenos.push(row.alergeno.Nombre);

              });
              stralergenos.join(',');
              $('#resultadoConsulta').empty().append('<div id="busqueda" class="card">'+
                '<div class="card-content">'+
                '<div class="card-title row">'+
                '<span class="col s10">Reserva de '+ data.Nombre + ' ' + data.Apellidos +'</span>'+
                '<a id="close" class="col s2 waves-effect material-icons">close</a>'+
                '</div>'+
                '<div class ="row">'+
                '<p class="col s5 offset-s1 left-align"><strong>Fecha Servicio:</strong> '+ data.Servicio.FechaServicio +'</p> '+
                '<p class="col s5 left-align"><strong>Correo:</strong> '+ data.Correo + '</p>'+
                '</div>'+
                '<div class="row">'+
                '<p class="col s5 offset-s1 left-align"><strong>Hora prevista de llegada: '+Utils.timeStringFormat(Utils.converToDate(data.HoraLlegada.date))+'</p>'+
                '<p class="col s5  left-align"><strong>Número de personas: '+ data.NPersonas +'</p>'+
                '</div>'+

                '<div class="row">'+
                '<p class="col s5 left-align offset-s1"><strong>Teléfono:</strong> '+ data.Telefono + '</p>'+
                '<p class="col s5 left-align"><strong>Alérgenos:</strong> '+ stralergenos + '</p>'+
                '</div>'+
                '<div class="row">'+
                '<p class="col s12"><strong>Observaciones:</strong> ' + data.Observaciones + '</p>'+
                '</div>'+
                '</div>'+
                '<div class="card-action">'+
                '<a id="anular" class="button especial2" href="#modal2">Cancelar</a>'+
                '<a id="editarReserva" class="button especial" href="#">Editar</a>'+
                '<a id="vermenu" class="button especial" href="#">Ver Menú</a>'+
                '</div>'+
              '</div>').css('display','none').fadeIn("slow");
              $('#close').click(function(){
                $('#resultadoConsulta').fadeOut('slow');

              });
              $('#vermenu').click(function(){
                window.open(window.location.origin + '/menu/'+ data.Servicio.id);
              })
              $("#editarReserva").click(function(){

                let checkbox = $('#check input'),
                alergenos = "",
                hora = Utils.converToDate(Reservas.consultaCache.HoraLlegada.date);
                console.log(hora);
                $("#busqueda").css("display", "none");
                $('#plazas').val(Reservas.consultaCache.NPersonas);
                $('#horallegada').val(Utils.timeStringFormat(hora));
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
              });
          });

        }); //buscar

        $("#guardar").click(function(event){
          //event.preventDefault();
          let id = Reservas.consultaCache.Id,
            hora = $('#horallegada').val(),
            npersonas = ''+$('#plazas').val(),
            nombre = $("#name").val(),
            apellidos = $("#ap").val(),
            correo = $("#email").val(),
            telefono = $("#tlfn").val(),
            observaciones = $("#observaciones").val(),
            checkbox = $("#check input"),
            servicio = Reservas.consultaCache.Servicio.id;
            console.log(npersonas);
            console.log(hora);
            console.log(nombre);
            console.log(apellidos);
            console.log(correo);
            console.log(telefono);
            console.log(observaciones);
            alergenos = [];



            $.each(checkbox, function(indice, v){
              console.log(v);
              if (v.checked) {
                alergenos.push(v.getAttribute('id'));
              }
            });
            //validar
            if(Reservas.validateInput() && Utils.timeValidate(hora)){
              Reservas.edit(id,nombre,apellidos,correo,telefono,observaciones,alergenos,npersonas,hora,servicio);
              $('.modal').modal();
              $('#modal1').modal('open');
            }


        });
      });

    }//si en consultar
    // Si estamos en reservas
    if (window.location.pathname === '/reservas') {
      let fecha,hora,plazas,nombre,apellidos,correo,telefonos,checkbox,observaciones;
      fechaActual = new Date();
      Calendar.mes = new Date().getMonth();
      Calendar.year = new Date().getFullYear();
      Servicios.Get(function(){
        Calendar.renderCalendar(Servicios.ServiciosCache);
      })
      Calendar.ToggleButtons();

      //Servicios.add(new Date(),25);
      $('#diaAnterior').click(function(event){
        let date;
        event.preventDefault();

        if (Calendar.mes === 0) {
          Calendar.year = Calendar.year - 1;
          Calendar.mes = 11;
          console.log(Calendar.mes);

        }else{
          Calendar.mes -= 1;
        }

        Calendar.ToggleButtons();
        Calendar.renderCalendar(Servicios.ServiciosCache);

      });
      $('#diaSiguiente').click(function(event){
        event.preventDefault();
        if (Calendar.mes === 11) {
          Calendar.year += 1;
          console.log(Calendar.year);
          console.log("he cambiado al año siguiente");
          Calendar.mes = 0;
        }else{
          Calendar.mes += 1;
        }

        Calendar.ToggleButtons();
        Calendar.renderCalendar(Servicios.ServiciosCache);
      })
      $('#atras0').click(function(){
        $('#datos1').fadeOut("slow");
        Utils.arriba();
        Utils.mostrar($('#datos0'));
      });
        $('#atras1').click(function(){
          $('#datos2').fadeOut("slow");
          Utils.arriba();
          Utils.mostrar($('#datos0'))
        });
        $('#siguiente').click(function(){
          fecha = $('#fecha').val();
          Reservas.horallegada = $('#horallegada').val();
          Reservas.plazas = $('#plazas').val();

          if (Utils.timeValidate(Reservas.horallegada)==false) {
            $('#errorhora').fadeIn('slow');
          }else{
            console.log($('#plazas').attr('max'));
            if (Reservas.MaxValidate(parseInt(Reservas.plazas),parseInt($('#plazas').attr('max')))===false) {
                $('#errorpersonas').fadeIn('slow');
            }else{
              $('#datos2').fadeOut("slow");
              $('#checkbox').children('input');
              Utils.arriba();
              Utils.mostrar($('#datos3'));
            }

          }


        });
        $('#atras2').click(function(){
          $('#datos3').fadeOut("slow");
          Utils.arriba();
          Utils.mostrar($('#datos2'));
        });
        $('#reservar').click(function(){

          if(Reservas.validateInput() == false){
            console.log('error');
            return
          }
          $('#datos3').fadeOut("slow");
          $('#parte1').removeClass('ubicacion');
          Reservas.nombre = $('#name').val();
          Reservas.apellidos = $('#ap').val();
          Reservas.correo = $('#email').val();
          Reservas.telefono = $('#tlfn').val();
          Reservas.observaciones = $('#observaciones').val();
          if ($('#boletin')[0].checked) {
              Reservas.Suscrito = 1;
          }
          checkbox = [];
          $.each($('#check input'),function(index,value){
            if(value.checked  == true){
              checkbox.push(value.id);
            }
          });
          console.log(checkbox);
          Reservas.alergenos = checkbox;
          $('#datosparaconfirmar').empty().append('<p class="col s6 left-align"> <strong>Nombre:</strong> '+ Reservas.nombre +'</p>')
          .append('<p class="col s6 left-align"><strong>Apellidos:</strong> '+ Reservas.apellidos + '</p>')
          .append('<p class="col s6 left-align"><strong>Correo:</strong> '+ Reservas.correo + '</p>')
          .append('<p class="col s6 left-align"><strong>Teléfono:</strong> '+ Reservas.telefono + '</p>')
          .append('<p class="col s6 left-align"><strong>Observaciones:</strong> '+ Reservas.observaciones + '</p>');
          p = $('<p class="col s6 left-align"><strong> Alérgenos: </strong> </p>');
          Reservas.alergenos.forEach(function(value,index,array){
            if (index < array.length - 1) {
              p.text(p.text() +  value + ',');
            }else {
              p.text(p.text() +  value);

            }
          });
          $('#datosparaconfirmar').append(p);
          console.log(Reservas + ' Confirmacion');
          $('#parte2').addClass('ubicacion');
          Utils.arriba();
          Utils.mostrar($('#confirmacion'));
        });
        $('#atras3').click(function(){
          $('#confirmacion').fadeOut("slow");
          $('#parte2').removeClass('ubicacion');
          $('#parte1').addClass('ubicacion');
          Utils.arriba();
          Utils.mostrar($('#datos3'));
        });
        $('#confirmar').click(function(){
          Reservas.add(Reservas.nombre,
            Reservas.apellidos,
            Reservas.correo, Reservas.telefono,
            Reservas.observaciones, Reservas.alergenos
            ,Reservas.idServicio,
            Reservas.EstadoReserva,
            Reservas.plazas, Reservas.horallegada,Reservas.Suscrito ,function(data){
                console.log(data.error);
                if(data.error === undefined){
                  $('#descargar').click(function(){
                    //window.location.pathname = 'pdf/'+data.Id;
                    window.open(window.location.origin+"/pdf/"+ data.Id);
                  });
                  $('#confirmacion').fadeOut("slow");
                  $('#parte2').removeClass('ubicacion');
                  $('#parte3').addClass('ubicacion');
                  Utils.arriba();
                  Utils.mostrar($('#realizado'))
                }else{
                  $('#erroradd').text(data.error).fadeIn(1000);
                }
            });

          ;
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
    horallegada: null,
    HoraSugerida: null,
    EstadoReserva:null,
    npersonas: null,
    ReservasCache: null,
    consultaCache:null,
    reservaCache: null,
    Suscrito: 0,
    MaxValidate: function(value,max){
      if (max >= value) {
        return true;
      }else{
        return false;
      }

    },
    delete: function(id){
      Utils.postAjax('api/reservas/delete',{
        id:id
      },function(data){
        return data;
      });


    },
    edit: function(id,nombre,apellidos,correo,telefono,observaciones,alergenos,npersonas,horallegada,idservicio){
        Utils.postAjax('api/reservas/edit',{
          id:id.trim(),
          nombre:nombre.trim(),
          apellidos:apellidos.trim(),
          correo:correo.trim(),
          telefono:telefono.trim(),
          alergenos:alergenos,
          observaciones:observaciones.trim(),
          npersonas:npersonas.trim(),
          horallegada:horallegada.trim(),
          idservicio:idservicio.trim()

        },function(data){

          return data;
        })
    },
    getReservasByCodReservas(codigo,success=null){
      Utils.postAjax('api/reservas/codreserva',{
        codigo:codigo.trim()
      },function(data){
        Reservas.consultaCache = data;
        success(data);
      });
    },
    // valida los campos del formulario de reservas
    validateInput: function(){
      let correcto = true;
      $('.errores').fadeOut("slow");
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
    add:function(nombre,apellidos,correo,telefono,observaciones,alergenos,servicio,estado,npersonas,horallegada,suscrito,success = null){
      Utils.postAjax('api/reservas/add',{
        nombre: nombre.trim(),
        apellidos: apellidos.trim(),
        telefono: telefono.trim(),
        correo: correo.trim(),
        observaciones: observaciones.trim(),
        servicio: servicio.trim(),
        estado: estado.trim(),
        alergenos: alergenos,
        npersonas: npersonas.trim(),
        suscrito: suscrito,
        horallegada: horallegada.trim(),
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
     ServiciosCache: null,
     PlazasDisponibles: null,
     Plazas: null,
     //datos de los servicios guardados en cache;
     //devuelve los servicios
     Get: function(success = null){
       Utils.getAjax('/api/servicios',function(data){
         Servicios.ServiciosCache = data;
         success();
       });
     },
     //servicios segun una fecha
     GetByFecha: function(fecha){
       Utils.postAjax('/api/servicios/fecha',{
         fecha:fecha
       },function(data){
         return data;
       });
     },
     //devuelve las fechas de los servicios
     GetFechas: function(){
       let datos = Servicios.ServiciosCache;
       let fechas = [];
       let fecha;
       $.each(datos,function(value){
         fecha = Utils.converToDate(value.FechaServicio);
       })
     },
     //añade un servicio
     add: function(date,plazas){
       Utils.postAjax('api/servicios/add',{
         Plazas:plazas,
         FechaServicio: Utils.datePostformat(date)
       },function(){
         return true
       })
     },
     //devuelve las plazas ocupadas de un servicio
     getPlazasOcupadas: function(id, success = null){
       Utils.postAjax('api/servicios/plazas',{
         id: id
       },function(data){
         success(data);
         console.log(data);
       })
     }
   };
   Calendar = {
     mes: null,
     year: null,
     meses: ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
     //habilita o deshabilita los botones del calendario en función del mes
     ToggleButtons: function(){
       if(Calendar.mes <= fechaActual.getMonth() && Calendar.year <= fechaActual.getFullYear()){
         $('#diaAnterior').attr('disabled','disabled');
       }else{
         $('#diaAnterior').removeAttr('disabled');
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
       paramDate.setFullYear(Calendar.year);
       //Recorremos los servicios
      $.each(servicios,function(index,value){
         // la fecha del Servicio en un date;

         dateServicio = Utils.converToDate(value.fechaservicio.date);

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

            Servicios.getPlazasOcupadas(row.Id, function(fila){
              let img = 'cardImageVerde.jpg';
              if (fila.length > 0) {
                  if (parseInt(fila[0].Plazas)-parseInt(fila[0].POcupadas) <= 0) {
                      img = 'cardImageRojo.jpg'
                  }
              }
              $('#datos1 .row #servicios').append("<div class='cards'><div class='card white horizontal'>"+
                "<div class='card-image'>"+
                  "<img src='bundles/reservas/img/"+img+"'>"+
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
                $('#datos1').fadeOut("slow");
                $('#datos2').fadeIn("slow");
              })
            });
          });
          return true;
        });
     },
     colorearBCalendario(divservicios){
       console.log(divservicios);
       if(divservicios.children('.bservicio').length === 1){
         divservicios.parent().children('.bcalendario').addClass(divservicios.children('.bservicio')[0].className).removeClass('bservicio');
       }
     },
     colorearServicios: function(servicio,bservicio){



           let porcentage = parseInt(servicio.plazas) * 0.8;
           if (servicio.plazasdisponibles <= 0) {
             bservicio.addClass("colorOcupado");
             bservicio.attr('estado','1');
             console.log("deberia de haber cambiado al color rojo");
           }else{
              if (parseInt(servicio.plazasocupadas) > porcentage) {
                  bservicio.addClass('colorPocoDisp');
                  bservicio.attr('estado','2');

              }else{
                  bservicio.addClass('colorDisponible');
                  bservicio.attr('estado','2');

              }
           }
         },
     renderCalendar: function(servicios){
        let max,fecha,fechas, semana;
        fecha = new Date();
        fecha.setDate(1);
        fecha.setMonth(Calendar.mes);
        fecha.setFullYear(Calendar.year);
        $('#mesActual').empty().text(Calendar.meses[Calendar.mes]+' '+Calendar.year);
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
          let domingo= null,colorestado = "green";

          tr = $('<tr id = "'+ index +'"></tr>');
          for (var i = 1; i < 7; i++) {
            if (value[i]) {
              console.log(value[i]);
              if (Calendar.EnableDate(value[i],servicios)===true) {
                td = $('<td id="'+i+'-'+Calendar.year+'"><button class="btn-floating btn-tiny bcalendario" >'+value[i]+'</button></td>')
                bservicios = "";
                divservicios = $('<div class="bservicios "></div>');
                servicios.forEach(function(row){
                    console.log(row + ' servicio');
                    let fecha= Utils.converToDate(row.fechaservicio.date),
                    hora=Utils.timeStringFormat(fecha);
                    console.log(fecha.getFullYear());
                    if (value[i]=== fecha.getDate() && Calendar.mes=== fecha.getMonth() && Calendar.year === fecha.getFullYear()) {
                      console.log('he entrado y esto no tiene sentido alguno');

                      bservicios = $('<button id ="'+ row.idservicios +'" plazas="'+row.plazas+'"" disponibles="'+row.plazasdisponibles+'" class="bservicio btn-floating btn-tiny">'+row.nombre+' (<strong id="horaCalendar">'+hora+'</strong>)'+'</button>');
                      divservicios.append(bservicios);
                      td.append(divservicios);
                      Calendar.colorearServicios(row, bservicios,value[i]);
                   }
                });
                tr.append(td);
                Calendar.colorearBCalendario(divservicios);
              }else{
                tr.append('<td id="'+i+'">'+value[i]+'</td>');
              }
            }else{
              tr.append('<td></td>');
            }
          }
          if (value[0]) {
            if (Calendar.EnableDate(value[0],servicios)===true) {
              td = $('<td id="0"><button class="btn-floating btn-tiny bcalendario" >'+value[0]+'</button></td>')
              bservicios = "";
              divservicios = $('<div class="bservicios"></div>');
              servicios.forEach(function(row){

                let fecha= Utils.converToDate(row.fechaservicio.date),
                hora=Utils.timeStringFormat(fecha);
                  if (value[0]=== fecha.getDate() && Calendar.mes === fecha.getMonth() && fechaActual.getFullYear() === fecha.getFullYear()) {

                    bservicios = $('<button id ="'+ row.idservicios +'" plazas="'+row.plazas+'"" disponibles="'+row.plazasdisponibles+'" class="bservicio btn-floating btn-tiny">'+row.nombre+' ('+'<strong id="horaCalendar">'+hora+'</strong>'+')'+'</button>');
                    divservicios.append(bservicios);
                    Calendar.colorearServicios(row, bservicios);

                  }
                  td.append(divservicios);
              });
              tr.append(td);
              Calendar.colorearBCalendario(divservicios);
            }else{
              tr.append('<td id="'+i+'">'+value[0]+'</td>')
            }
          }
          $('#calendar').append(tr);
          $('.bservicio').click(function(){
            Reservas.idServicio = $(this).attr('id');
            Reservas.EstadoReserva = $(this).attr('estado');
            Reservas.HoraSugerida = $(this).children('#horaCalendar').text();
            Servicios.PlazasDisponibles = $(this).attr('disponibles');
            Servicios.Plazas = $(this).attr('plazas');
            console.log(Reservas.HoraSugerida);
            if (Reservas.EstadoReserva == 2) {
              $('#datos0').css("display",'none');
              $('#horallegada').val(Reservas.HoraSugerida);
              $('#disponibles').text(Servicios.PlazasDisponibles);
              $('#plazas').attr('max',Servicios.PlazasDisponibles);
              $('#datos2').css("display",'block');
            }else{
              $('#modal1').modal();
              $('#modal1').modal('open');
              $('#Si').click(function(){
                $('#datos0').css("display",'none');
                $('#horallegada').val(Reservas.HoraSugerida);
                $('#disponibles').text(Servicios.PlazasDisponibles);
                $('#datos2').css("display",'block');
                $('#modal1').modal('close');
              });
              $('#No').click(function(){
                $('#modal1').modal('close');
              });

            }
          })
        });
        /*$(".bcalendario").mouseenter(function(){
          $('.modal').modal();
          $('#modal').modal('open');
        });*/


     }
   };
   Utils = {
    arriba: function(){
      $('html, body').stop().animate({scrollTop:0},"slow");
    },
    timeValidate: function(str){
      $patron = new RegExp("^([0-1][0-9])|([0-2][0-3]):[0-5][0-9]$");
      return $patron.test(str)
    },
    timeStringFormat: function(date){
      let hora = ''+date.getHours(),
      minutes = ''+date.getMinutes();
      if(hora.length === 1){
        hora = '0'+hora;
      }
      if (minutes.length === 1) {
        minutes = '0'+minutes;
      }

      return hora +':' + minutes;
    },
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
      fecha = fecha.replace('-','/');
      fecha = fecha.replace('-','/');
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
    mostrar: function(object){
      $('.card').css('display','none');
      window.setTimeout(function(){
        object.css('display','block');
        $('.card').fadeIn('slow');
      },100);
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
