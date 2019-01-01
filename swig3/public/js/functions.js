function showErrorMessages(error, element) {
    console.info($('#employee_registration').valid());
    element.next('span').html(error[0].outerText);   
}