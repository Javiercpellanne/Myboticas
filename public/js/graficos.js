function vhorario()
{
  var inicio = $("#inicio").val();
  var fin = $("#fin").val();

  var horas = [];
  var ventas = [];
  $.post('ventash',{inicio,fin},function(data){
    //console.log(data);
    var c = JSON.parse(data);
    $.each(c, function(i,item){
        horas.push(item.horas);
        ventas.push(item.ventas);
    });

    var barChartData = {
      labels  : horas,
      datasets: [
        {
          label               : 'Ventas Realizadas',
          backgroundColor     : 'rgba(60,141,188,0.9)',
          borderColor         : 'rgba(60,141,188,0.8)',
          pointRadius          : false,
          pointColor          : '#3b8bba',
          pointStrokeColor    : 'rgba(60,141,188,1)',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(60,141,188,1)',
          data                : ventas
        }
      ]
    }

    var barChartCanvas = $('#barChart').get(0).getContext('2d')
    var barChartOptions = {
      responsive              : true,
      maintainAspectRatio     : false,
      datasetFill             : false,
      title                   : {
          display: true,
          text: 'Ventas por Horarios'
      }
    }

    var barChart = new Chart(barChartCanvas, {
      type: 'bar',
      data: barChartData,
      options: barChartOptions
    })
  });
}

function vmensual()
{
  var fechas = [];
  var ventas = [];
  var nventas = [];
  $.post('inicio/ventasm',function(data){
    //console.log(data);
    var c = JSON.parse(data);
    $.each(c, function(i,item){
        fechas.push(item.fechas);
        ventas.push(item.ventas);
        nventas.push(item.nventas);
    });

    var barChartData = {
      labels  : fechas,
      datasets: [
        {
          label               : 'Total comprobantes electronicos',
          backgroundColor     : 'rgba(60,141,188,0.9)',
          borderColor         : 'rgba(60,141,188,0.8)',
          pointRadius          : false,
          pointColor          : '#3b8bba',
          pointStrokeColor    : 'rgba(60,141,188,1)',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(60,141,188,1)',
          data                : ventas
        },
        {
          label               : 'Total notas ventas',
          backgroundColor     : 'rgba(255,99,132,0.9)',
          borderColor         : 'rgba(255,99,132,0.8)',
          pointRadius          : false,
          pointColor          : '#3b8bba',
          pointStrokeColor    : 'rgba(255,99,132,1)',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(255,99,132,1)',
          data                : nventas
        }
      ]
    }

    var barChartCanvas = $('#barVenta').get(0).getContext('2d')
    var barChartOptions = {
      responsive              : true,
      maintainAspectRatio     : false,
      datasetFill             : false,
      title                   : {
          display: true,
          text: 'Ventas Emitidas'
      }
    }

    var barChart = new Chart(barChartCanvas, {
      type: 'bar',
      data: barChartData,
      options: barChartOptions
    })
  });
}

function canual()
{
  var fechas = [];
  var compras = [];
  $.post('inicio/comprasa',function(data){
    //console.log(data);
    var c = JSON.parse(data);
    $.each(c, function(i,item){
        fechas.push(item.fechas);
        compras.push(item.compras);
    });

    var barChartData = {
      labels  : fechas,
      datasets: [
        {
          label               : 'Total compras',
          backgroundColor     : 'rgba(60,141,188,0.9)',
          borderColor         : 'rgba(60,141,188,0.8)',
          pointRadius          : false,
          pointColor          : '#3b8bba',
          pointStrokeColor    : 'rgba(60,141,188,1)',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(60,141,188,1)',
          data                : compras
        }
      ]
    }

    var barChartCanvas = $('#barCompra').get(0).getContext('2d')
    var barChartOptions = {
      responsive              : true,
      maintainAspectRatio     : false,
      datasetFill             : false,
      title                   : {
          display: true,
          text: 'Compras Emitidas'
      }
    }

    var barChart = new Chart(barChartCanvas, {
      type: 'bar',
      data: barChartData,
      options: barChartOptions
    })
  });
}

function nventa()
{
  $.post('inicio/nventac',function(data){
    //console.log(data);
    var item = JSON.parse(data);

    var name= ['Total Cobrado','Pendiente de cobro'];
    var marks= [item.contado, item.credito];

    var chartdata = {
      labels: name,
      datasets: [{
        label: 'Nota Venta',
        backgroundColor : ['rgb(54,162,235)','rgb(255,99,132)'],
        hoverBackgroundColor: 'rgba(230,236,235, 0.75)',
        hoverBorderColor: 'rgba(230,236,235,0.75)',
        data: marks

      }]
    };
    var graphTarget = $("#doughnutNventa");
    var barGraph = new Chart(graphTarget, {
      type: 'doughnut',
      data: chartdata,
    });
  });
}

function comprobante()
{
  $.post('inicio/comprobantec',function(data){
    //console.log(data);
    var item = JSON.parse(data);

    var name= ['Total Cobrado','Pendiente de cobro'];
    var marks= [item.contado, item.credito];

    var chartdata = {
      labels: name,
      datasets: [{
        label: 'Comprobante Electronico',
        backgroundColor : ['rgb(54,162,235)','rgb(255,99,132)'],
        hoverBackgroundColor: 'rgba(230,236,235, 0.75)',
        hoverBorderColor: 'rgba(230,236,235,0.75)',
        data: marks

      }]
    };
    var graphTarget = $("#doughnutComprobante");
    var barGraph = new Chart(graphTarget, {
      type: 'doughnut',
      data: chartdata,
    });
  });
}

function compra()
{
  $.post('inicio/comprac',function(data){
    var item = JSON.parse(data);

    var name= ['Total Pagado','Pendiente de pago'];
    var marks= [item.contado, item.credito];

    var chartdata = {
      labels: name,
      datasets: [{
        label: 'Compra',
        backgroundColor : ['rgb(54,162,235)','rgb(255,99,132)'],
        hoverBackgroundColor: 'rgba(230,236,235, 0.75)',
        hoverBorderColor: 'rgba(230,236,235,0.75)',
        data: marks

      }]
    };
    var graphTarget = $("#doughnutCompra");
    var barGraph = new Chart(graphTarget, {
      type: 'doughnut',
      data: chartdata,
    });
  });
}

function productos()
{
  $.post('productos',function(data){
    //console.log(data);
    var item = JSON.parse(data);

    var name= ['Total Generico','Total Marca'];
    var marks= [item.generico, item.marca];

    var chartdata = {
      labels: name,
      datasets: [{
        label: 'Productos',
        backgroundColor : ['rgb(54,162,235)','rgb(255,99,132)'],
        hoverBackgroundColor: 'rgba(230,236,235, 0.75)',
        hoverBorderColor: 'rgba(230,236,235,0.75)',
        data: marks

      }]
    };
    var graphTarget = $("#doughnutProductos");
    var barGraph = new Chart(graphTarget, {
      type: 'doughnut',
      data: chartdata,
    });
  });
}

function clasificacion()
{
  $.post('clasificacion',function(data){
    var item = JSON.parse(data);

    var name= ['Total Generico','Total Marca','Total Otros'];
    var marks= [item.generico, item.marca, item.otro];

    var chartdata = {
      labels: name,
      datasets: [{
        label: 'Clasificacion',
        backgroundColor : ['rgb(54,162,235)','rgb(255,99,132)','rgb(209,209,46)'],
        hoverBackgroundColor: 'rgba(230,236,235, 0.75)',
        hoverBorderColor: 'rgba(230,236,235,0.75)',
        data: marks

      }]
    };
    var graphTarget = $("#doughnutClasificacion");
    var barGraph = new Chart(graphTarget, {
      type: 'doughnut',
      data: chartdata,
    });
  });
}
