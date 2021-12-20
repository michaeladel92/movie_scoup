/*===============
GLOBAL VARS
===============*/ 
const navMenuCart = document.getElementById('sort-list'),
      innerMenu = document.querySelector('.inner-menu'),
      loading   = document.querySelector('.loading')
      // scroll top when refresh
      if (history.scrollRestoration) {history.scrollRestoration = 'manual';}
       else {
            window.onbeforeunload = function () {
            window.scrollTo(0, 0);
            }
      }

/*===============
EVENT LISTNER
===============*/
// Dropdown Menu
navMenuCart.addEventListener('mouseenter', e => {
  if(e.target.firstElementChild.lastElementChild.classList.contains('fa-sort-down'))
    e.target.firstElementChild.lastElementChild.className = 'fas fa-sort-up'
    innerMenu.classList.add('active')      
  }
)

navMenuCart.addEventListener('mouseleave', e => {
  if(e.target.firstElementChild.lastElementChild.classList.contains('fa-sort-up'))
  e.target.firstElementChild.lastElementChild.className = 'fas fa-sort-down'
  innerMenu.classList.remove('active')  
  }
)


/*===============
FUNCTIONS
===============*/
// display loading
function displayLoad(){
  loading.classList.add('show')
}

//hide loading
function hideLoad(){
  loading.classList.remove('show')
}

// clear All input Fields
function clearAllFields(resetForm){
  
  let allMessagesNotifications = document.querySelectorAll('.message')
  let allErrSuccElements = document.querySelectorAll("input, textarea, select")
  
  allMessagesNotifications.forEach(message => {
    if(!message.classList.contains('notifications'))
        message.style.visibility = 'hidden';
        message.innerText = '';
  })
  allErrSuccElements.forEach(el => {
    if(el.classList.contains('success') || el.classList.contains('error')){
      el.classList.remove('success','error');
    }
  })

  resetForm.reset()
  reg_notify.classList.remove('show')
}






