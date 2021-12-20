/*===============
GLOBAL VARS
===============*/
const editUserForm = document.getElementById('edit-user'),
  notificationEditForm = document.querySelector('.notifications_global_api')

hideLoad()
// displayLoad()
/*===============
event listner
===============*/

if (editUserForm !== null) {
  editUserForm.addEventListener('submit', e => {
    displayLoad()
    e.preventDefault()
    const data = new FormData(editUserForm)
    data.append('trigger', 'edit_user')
    const xml = new XmlFetch()
    xml.postFetch('./controller/edit_profile.php', data)
      .then(res => {
        const notifications = JSON.parse(res)
        if (notifications.error) {
          if (notifications.error === '') {
            window.location.href = 'profile.php'
          } else {
            notificationEditForm.innerText = notifications.error
            notificationEditForm.classList.remove('show', 'success')
            notificationEditForm.classList.add('show', 'error')
            hideLoad()
            setTimeout(() => {
              notificationEditForm.classList.remove('show')
            }, 3000)
          }
        } else {
          window.location.href = 'profile.php'
        }
      })
      .catch(error => console.log(error))
  })
}