/*===============
GLOBAL VARS
===============*/
const loginBtnSwitch = document.querySelector('.login'),
  regBtnSwitch = document.querySelector('.register'),
  registerationForm = document.querySelector('.registeration_form'),
  loginForm = document.querySelector('.login_form'),
  // Register Form Input
  regName = document.getElementById('reg_name'),
  regEmail = document.getElementById('reg_email'),
  regPassword = document.getElementById('reg_password'),
  regConfirmPass = document.getElementById('reg_confirm_pass'),
  regGender = document.getElementById('reg_gender'),
  regAbout = document.getElementById('reg_about'),
  imageProfile = document.getElementById('image'),
  aboutLengthCalc = document.querySelector('.max-length'),
  reg_notify = document.getElementById('nofitication_register'),
  // Login Form Input  
  login_notify = document.getElementById('nofitication_login'),
  //Form BTN Submit[form itself]
  registerBtn = document.getElementById('register_form'),
  loginBtn = document.getElementById('loginForm')



/*===============
EVENTS
===============*/
// login Switch
loginBtnSwitch.addEventListener('click', e => {
  displayLoad()
  if (e.target.classList.contains('login') && !e.target.classList.contains('active'))
    clearAllFields(registerBtn)
  e.target.classList.add('active')
  loginForm.classList.add('active')
  regBtnSwitch.classList.remove('active')
  registerationForm.classList.remove('active')
  hideLoad()
})
// Register Switch
regBtnSwitch.addEventListener('click', e => {
  displayLoad()
  aboutLengthCalc.innerText = '0 / 500'
  if (e.target.classList.contains('register') && !e.target.classList.contains('active'))
    clearAllFields(registerBtn)

  e.target.classList.add('active')
  registerationForm.classList.add('active')
  loginBtnSwitch.classList.remove('active')
  loginForm.classList.remove('active')
  hideLoad()
})

// Registeration Form
let max, min
// name
regName.addEventListener('input', e => {
  max = 50
  min = 3
  if (MaxMinLengthCheck(e, max, min)) {
    regexNoSymbols(e)
  }
})
// email
regEmail.addEventListener('input', e => {
  max = 70
  min = 10

  if (MaxMinLengthCheck(e, max, min)) {
    regexValidEmail(e)
  }
})
// password
regPassword.addEventListener('input', e => {
  max = 50
  min = 6
  MaxMinLengthCheck(e, max, min)
})
// confirm pass
regConfirmPass.addEventListener('input', e => {
  max = 50
  min = 6
  if (MaxMinLengthCheck(e, max, min)) {
    passwordMatch(e, regPassword.value.trim())
  }


})
// gender
regGender.addEventListener('change', e => {
  checkGenderValidate(e)
})
// About length
regAbout.addEventListener('input', e => {
  let inputLength = +e.target.value.trim().length
  aboutLengthCalc.innerText = `${inputLength} / 500`

  if (inputLength > 500) {
    aboutLengthCalc.classList.add('error')
    e.target.classList.add('error')
  }
  else {
    aboutLengthCalc.classList.remove('error')
    e.target.classList.remove('error')
    e.target.classList.add('success')

  }
})

/*===============
FUNCTIONS
===============*/

// check max and min length
function MaxMinLengthCheck(e, max = 100, min = 5) {
  let input = e.target,
    value = input.value.trim(),
    message = e.target.nextElementSibling,
    name = e.target.getAttribute('data-names')

  if (value == "") {
    message.innerText = `${name} Can not be empty`
    message.style.visibility = "visible"
    input.classList.add('error')
  }
  else if (value.length < min) {
    message.innerText = `${name} must not be less than ${min} Char`
    message.style.visibility = "visible"
    input.classList.add('error')
  }
  else if (value.length > max) {
    message.innerText = `${name} must not be more than ${max} Char`
    message.style.visibility = "visible"
    input.classList.add('error')
  }
  else {
    message.innerText = ''
    message.style.visibility = "hidden"
    input.classList.remove('error')
    input.classList.add('success')
    return true
  }
}
// No Symbol allowed
function regexNoSymbols(e) {
  let input = e.target,
    message = e.target.nextElementSibling,
    name = e.target.getAttribute('data-names'),
    regex = /[\[^\'$%^&*()}{@:\'#"÷؛؟×،~?><>,;@\|\-=\-_+\-\`\]]/
  value = input.value.trim()
  if (regex.test(value.toLowerCase())) {
    message.innerText = `${name} must not contain symbols`
    message.style.visibility = "visible"
    input.classList.add('error')
  }
  else if (!isNaN(value[0])) {
    regName.nextElementSibling.innerText = `${name} can not start with a number`
    message.style.visibility = "visible"
    input.classList.add('error')
  }
  else {
    message.innerText = ''
    message.style.visibility = "hidden"
    input.classList.remove('error')
    input.classList.add('success')
    return true
  }
}
// Check Valid Email
function regexValidEmail(e) {
  let input = e.target,
    message = e.target.nextElementSibling,
    name = e.target.getAttribute('data-names'),
    regex = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/

  if (regex.test(input.value.toLowerCase())) {
    message.innerText = ''
    message.style.visibility = "hidden"
    input.classList.remove('error')
    input.classList.add('success')
    return true
  }
  else {
    message.innerText = `${name} is not valid`
    message.style.visibility = "visible"
    input.classList.add('error')
  }
}
// password match
function passwordMatch(e, password) {
  let input = e.target,
    message = e.target.nextElementSibling

  if (input.value.trim() === password) {
    message.innerText = ''
    message.style.visibility = "hidden"
    input.classList.remove('error')
    input.classList.add('success')
    return true
  }
  else {
    message.innerText = `Password Not Match`
    message.style.visibility = "visible"
    input.classList.add('error')
  }


}
// gender validate
function checkGenderValidate(e) {
  let input = e.target,
    value = input.value.trim(),
    message = e.target.nextElementSibling

  if (value === 'male' || value === 'female') {
    message.innerText = ''
    message.style.visibility = "hidden"
    input.classList.remove('error')
    input.classList.add('success')
    return true
  }
  else {
    message.innerText = `Please choose gender`
    message.style.visibility = "visible"
    input.classList.add('error')
  }
}
// Validate All Fields in Register
function validateAllFields() {
  const name = regName.value.trim(),
    email = regEmail.value.trim(),
    password = regPassword.value.trim(),
    conPass = regConfirmPass.value.trim(),
    gender = regGender.value.trim(),
    about = regAbout.value.trim()

  // name
  let regexText = /[\[^\'$%^&*()}{@:\'#"÷؛؟×،~?><>,;@\|\-=\-_+\-\`\]]/,
    regexEmail = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
    nameMax = 50,
    nameMin = 3,
    emailMax = 70,
    emailMin = 10,
    passMax = 50,
    passMin = 6,
    validation_name = false,
    validation_email = false,
    validation_password = false,
    validation_conPass = false,
    validation_gender = false,
    validation_about = false



  if (name === "") {
    regName.nextElementSibling.innerText = `${regName.getAttribute('data-names')} Can not be empty`
    regName.nextElementSibling.style.visibility = "visible"
    regName.classList.add('error')
  }
  else if (name.length < nameMin) {
    regName.nextElementSibling.innerText = `${regName.getAttribute('data-names')} must not be less than ${nameMin} Char`
    regName.nextElementSibling.style.visibility = "visible"
    regName.classList.add('error')
  }
  else if (name.length > nameMax) {
    regName.nextElementSibling.innerText = `${regName.getAttribute('data-names')} must not be more than ${nameMax} Char`
    regName.nextElementSibling.style.visibility = "visible"
    regName.classList.add('error')
  }
  else if (regexText.test(name.toLowerCase())) {
    regName.nextElementSibling.innerText = `${regName.getAttribute('data-names')} must not contain symbols`
    regName.nextElementSibling.style.visibility = "visible"
    regName.classList.add('error')
  }
  else if (!isNaN(name[0])) {
    regName.nextElementSibling.innerText = `${regName.getAttribute('data-names')} can not start with a number`
    regName.nextElementSibling.style.visibility = "visible"
    regName.classList.add('error')
  }
  else {
    regName.nextElementSibling.innerText = ''
    regName.nextElementSibling.style.visibility = "hidden"
    regName.classList.remove('error')
    regName.classList.add('success')
    validation_name = true;
  }

  // email
  if (email === "") {
    regEmail.nextElementSibling.innerText = `${regEmail.getAttribute('data-names')} Can not be empty`
    regEmail.nextElementSibling.style.visibility = "visible"
    regEmail.classList.add('error')

  }
  else if (email.length < emailMin) {
    regEmail.nextElementSibling.innerText = `${regEmail.getAttribute('data-names')} must not be less than ${emailMin} Char`
    regEmail.nextElementSibling.style.visibility = "visible"
    regEmail.classList.add('error')

  }
  else if (email.length > emailMax) {
    regEmail.nextElementSibling.innerText = `${regEmail.getAttribute('data-names')} must not be more than ${emailMax} Char`
    regEmail.nextElementSibling.style.visibility = "visible"
    regEmail.classList.add('error')

  }
  else if (!regexEmail.test(email.toLowerCase())) {
    regEmail.nextElementSibling.innerText = `${regEmail.getAttribute('data-names')} is not valid`
    regEmail.nextElementSibling.style.visibility = "visible"
    regEmail.classList.add('error')
  }
  else {
    regEmail.nextElementSibling.innerText = ''
    regEmail.nextElementSibling.style.visibility = "hidden"
    regEmail.classList.remove('error')
    regEmail.classList.add('success')
    validation_email = true
  }

  //Password 
  if (regPassword === "") {
    regPassword.nextElementSibling.innerText = `${regPassword.getAttribute('data-names')} Can not be empty`
    regPassword.nextElementSibling.style.visibility = "visible"
    regPassword.classList.add('error')

  }
  else if (password.length < passMin) {
    regPassword.nextElementSibling.innerText = `${regPassword.getAttribute('data-names')} must not be less than ${emailMin} Char`
    regPassword.nextElementSibling.style.visibility = "visible"
    regPassword.classList.add('error')

  }
  else if (password.length > passMax) {
    regPassword.nextElementSibling.innerText = `${regPassword.getAttribute('data-names')} must not be more than ${emailMax} Char`
    regPassword.nextElementSibling.style.visibility = "visible"
    regPassword.classList.add('error')

  }
  else {
    regPassword.nextElementSibling.innerText = ''
    regPassword.nextElementSibling.style.visibility = "hidden"
    regPassword.classList.remove('error')
    regPassword.classList.add('success')
    validation_password = true;
  }

  //Confirm Password 
  if (conPass === "") {
    regConfirmPass.nextElementSibling.innerText = `${regConfirmPass.getAttribute('data-names')} Can not be empty`
    regConfirmPass.nextElementSibling.style.visibility = "visible"
    regConfirmPass.classList.add('error')

  }
  else if (conPass.length < passMin) {
    regConfirmPass.nextElementSibling.innerText = `${regConfirmPass.getAttribute('data-names')} must not be less than ${emailMin} Char`
    regConfirmPass.nextElementSibling.style.visibility = "visible"
    regConfirmPass.classList.add('error')

  }
  else if (conPass.length > passMax) {
    regConfirmPass.nextElementSibling.innerText = `${regConfirmPass.getAttribute('data-names')} must not be more than ${emailMax} Char`
    regConfirmPass.nextElementSibling.style.visibility = "visible"
    regConfirmPass.classList.add('error')

  }
  else if (conPass.trim() !== password.trim()) {
    regConfirmPass.nextElementSibling.innerText = `Password Not Match`
    regConfirmPass.nextElementSibling.style.visibility = "visible"
    regConfirmPass.classList.add('error')
  }
  else {
    regConfirmPass.nextElementSibling.innerText = ''
    regConfirmPass.nextElementSibling.style.visibility = "hidden"
    regConfirmPass.classList.remove('error')
    regConfirmPass.classList.add('success')
    validation_conPass = true;
  }
  //GENDER Validate
  if (gender === 'male' || gender === 'female') {
    regGender.nextElementSibling.innerText = ''
    regGender.nextElementSibling.style.visibility = "hidden"
    regGender.classList.remove('error')
    regGender.classList.add('success')
    validation_gender = true;
  }
  else {
    regGender.nextElementSibling.innerText = `Please choose gender`
    regGender.nextElementSibling.style.visibility = "visible"
    regGender.classList.add('error')
  }
  //About Me
  aboutLengthCalc.innerText = `${about.length} / 500`
  if (about.length > 500) {
    aboutLengthCalc.classList.add('error')
  }
  else {
    aboutLengthCalc.classList.remove('error')
    validation_about = true
  }

  if (validation_name && validation_email && validation_password && validation_conPass && validation_gender && validation_about) {
    return true
  }

}



/*===============
API 
===============*/
// REGISTER
registerBtn.addEventListener('submit', function (e) {

  e.preventDefault();


  if (validateAllFields()) {
    displayLoad()
    // const data = `name=${regName.value}&email=${regEmail.value}&password=${regPassword.value}&conPass=${regConfirmPass.value}&gender=${regGender.value}&about=${regAbout.value}`

    const xml = new Xml()
    // STRINGS
    // xml.post('controller/register_process.php',data.trim(),(err,res) =>{
    //   if(res){
    //       let notification = JSON.parse(res)
    //       console.log(notification)
    //   } else{ 
    //       console.log(err)
    //   }
    // })

    // IMAGE
    let data = new FormData(registerBtn);
    // data.append("image", imageProfile.files[0]);
    // data.append("name", regName.value.trim());
    // data.append("email", regEmail.value.trim());
    // data.append("password", regPassword.value.trim());
    // data.append("conPass", regConfirmPass.value.trim());
    // data.append("gender", regGender.value.trim());
    // data.append("about", regAbout.value.trim());
    xml.post('controller/register_process.php', data, (err, res) => {
      if (res) {
        let notification = JSON.parse(res)

        if (notification.error) {
          hideLoad()
          reg_notify.innerText = notification.error
          reg_notify.classList.remove('success')
          reg_notify.classList.add('show', 'error')
          setTimeout(() => {
            reg_notify.classList.remove('show', 'error')
          }, 3000)

        } else {
          //success register
          window.location.href = 'index.php'
        }
      } else {
        console.log(err)
      }
    })

  } else {
    console.log('false')
  }

})
// LOGIN
loginBtn.addEventListener('submit', function (e) {
  displayLoad()
  e.preventDefault()
  let data = new FormData(this)
  let xml = new Xml()


  xml.post('controller/login_process.php', data, (err, res) => {
    if (res) {
      let notification = JSON.parse(res)
      if (notification.error) {
        hideLoad()
        login_notify.innerText = notification.error
        login_notify.classList.remove('success')
        login_notify.classList.add('show', 'error')
        if (notification.entry_attempt === 'failed') {
          setTimeout(() => {
            window.location.href = 'index.php'
          }, 3000)
        }

        setTimeout(() => {
          login_notify.classList.remove('show', 'error')
        }, 3000)
      } else {
        // success login
        window.location.href = 'index.php'
      }
    } else {
      alert(err)
    }
  })

})
