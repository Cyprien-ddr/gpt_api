

$(document).ready(function(){
$( "#dnslytics :submit" ).click(function( event ) {
  $( "#dnslytics" ).attr('action', "https://dnslytics.com/ip/" + $( "#dnslytics #name" ).val()).submit();
  event.preventDefault();
});
$( "#sslshopper :submit" ).click(function( event ) {
  $( "#sslshopper" ).attr('action', "https://www.sslshopper.com/ssl-checker.html#hostname=" + $( "#sslshopper #urlssl" ).val()).submit();
  event.preventDefault();
});


$("#wget").validate({
      rules: {
         "urlwget":{
            "required": true,
            "url": true
         }
      }
});

$("#sslshopper").validate({
      rules: {
         "urlssl":{
            "required": true,
            "url": true
         }
      }
});

$("#dnslytics").validate({
      rules: {
         "name":{
            "required": true,
            "IP4Checker": true
         }
      }
});
});


$.validator.addMethod('IP4Checker', function(value) {
        var ip = "^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$";
            return value.match(ip);
}, "Ce n'est pas une adresse ip v4 valide");
jQuery.extend(jQuery.validator.messages, {
    required: "Ce champ est requis",
    remote: "votre message",
    email: "votre message",
    url: "Ce n'est pas une url",
    date: "votre message",
    dateISO: "votre message",
    number: "votre message",
    digits: "votre message",
    creditcard: "votre message",
    equalTo: "votre message",
    accept: "votre message",
    maxlength: jQuery.validator.format("votre message {0} caractéres."),
    minlength: jQuery.validator.format("Ce champ doit contenir au moins {0} caractéres."),
    rangelength: jQuery.validator.format("votre message  entre {0} et {1} caractéres."),
    range: jQuery.validator.format("votre message  entre {0} et {1}."),
    max: jQuery.validator.format("votre message  inférieur ou égal à {0}."),
    min: jQuery.validator.format("votre message  supérieur ou égal à {0}.")
  });