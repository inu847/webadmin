const preloader = function() {
    $("#preloader").fadeIn('fast');
};
const afterpreloader = function() {
    $("#preloader").fadeOut('400');
};

const preloadersmall = function() {
    $('.preloader-small').css('display', 'block');
};
const afterpreloadersmall = function() {
    $('.preloader-small').fadeOut(300);
};

const preloadersubmit = function() {
    $('.submit-preloader').css('display', 'block');
};
const afterpreloadersubmit = function() {
    $('.submit-preloader').fadeOut(300);
};

const preloadContent = function() {
    $('.preloadContent').fadeIn(300);
};
const afterPreloadContent = function() {
    $('.preloadContent').fadeOut(300);
};

const submitForm = function() {
    $('.formLoader').fadeIn(300);
};
const afterSubmitForm = function() {
    $('.formLoader').fadeOut(300);
};

const formatCurrency=function(total){
    var result=total.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
    return result;
}
const removeFormatCurrency = function(total) {
    return total.replace(/[.,\s]/g, '');
}
const ValidURL = function(str) {
    var regex = /(http|https):\/\/(\w+:{0,1}\w*)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%!\-\/]))?/;
    if(!regex .test(str)) {
      return "false";
    } else {
      return "true";
    }
}