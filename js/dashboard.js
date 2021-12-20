displayLoad()
/*===============
GLOBAL VARS
===============*/
const triggerAddCategoryBtn = document.getElementById('add_category'),
  popUp = document.querySelector('.popup'),
  popupContainer = document.querySelector('.popup .popup-container'),
  apiNotification = document.querySelector('.notifications_global_api'),
  categoryTable = document.getElementById('category_table'),
  movieTable = document.getElementById('movie_table'),
  memberTable = document.getElementById('member_table'),
  dashboardNotice = document.querySelector('.dashboard_Notice'),
  searchCategoryInput = document.getElementById('search_category'),
  // searchMovieInput = document.getElementById('search_movie'),
  movieAddForm = document.getElementById('add_movie'),
  movieEditForm = document.getElementById('edit_movie'),
  //insight boxs
  view_count = document.getElementById('view_count'),
  publish_count = document.getElementById('publish_count'),
  comment_count = document.getElementById('comment_count'),
  user_count = document.getElementById('user_count'),
  pending_article_count = document.getElementById('pending_article_count'),
  pending_comment_count = document.getElementById('pending_comment_count'),
  // members box
  total_users_count = document.getElementById('total_users_count'),
  pending_users_count = document.getElementById('pending_users_count'),
  approved_users_count = document.getElementById('approved_users_count')




/*===============
EVENT LISTNER
===============*/
window.addEventListener('DOMContentLoaded', e => {
  // tinymce plugin
  tinymce.init({
    selector: '#movie_description',
    plugins: [
      "code",
      "media"
    ]
  })

  //get category lists
  if (categoryTable !== null) {
    getCategory()
  }
  // get movie Lists
  if (movieTable !== null) {
    getMovies()
  }
  // search query
  if (searchCategoryInput !== null) {
    searchQuery(searchCategoryInput)
  }
  hideLoad()
});
// Add category btn [trigger the popup]
if (triggerAddCategoryBtn !== null) {
  triggerAddCategoryBtn.addEventListener('click', e => {
    e.preventDefault()

    if (!popUp.classList.contains('show')) {
      displayLoad()
      paintAddCategoryPopUp()
      popUp.classList.add('show')
      hideLoad()
    }
  })
}
// BUBBLING > POPUP
popupContainer.addEventListener('click', e => {
  e.preventDefault()

  //popup btn [x] close 
  if (
    e.target.parentElement.classList.contains('close')
    ||
    e.target.classList.contains('option_no')
  ) {
    displayLoad()
    popUp.classList.remove('show')
    hideLoad()
  }
  //btn add category
  else if (e.target.classList.contains('add_category')) {
    displayLoad()
    const category = e.target.previousElementSibling.value.trim()
    const xml = new Xml()
    const data = `category=${category}&trigger=add_category`
    xml.postText('inc/dash_cat.php', data, (err, res) => {
      if (res) {
        let notifications = JSON.parse(res)
        if (notifications.error) {
          apiNotification.innerHTML = notifications.error
          apiNotification.classList.remove('success')
          apiNotification.classList.add('show', 'error')
          hideLoad()
          setTimeout(() => {
            apiNotification.classList.remove('show')
          }, 3000)
        } else {
          popUp.classList.remove('show')
          popupContainer.innerHTML = ''
          paintTableRow(notifications.id, notifications.name)
          apiNotification.innerHTML = notifications.success
          apiNotification.classList.remove('error')
          apiNotification.classList.add('show', 'success')
          //get category lists
          getCategory();
          hideLoad()
          setTimeout(() => {
            apiNotification.classList.remove('show')
          }, 3000)

        }
      } else {
        hideLoad()
        console.log(err)
      }
    })
  }
  //btn edit category
  else if (e.target.classList.contains('edit_category')) {
    displayLoad()
    const category = e.target.previousElementSibling.value.trim()
    const id = +e.target.previousElementSibling.previousElementSibling.value.trim()
    const xml = new Xml()
    const data = `category=${category}&id=${id}&trigger=edit_category`
    // let data = new FormData()
    // data.append('category', category)
    // data.append('id', id)
    // data.append('trigger', 'edit_category')

    xml.postText('inc/dash_cat.php', data, (err, res) => {
      if (res) {
        let notifications = JSON.parse(res)
        if (notifications.error) {
          apiNotification.innerHTML = notifications.error
          apiNotification.classList.remove('success')
          apiNotification.classList.add('show', 'error')
          hideLoad()
          setTimeout(() => {
            apiNotification.classList.remove('show')
          }, 3000)
        } else {
          popUp.classList.remove('show')
          popupContainer.innerHTML = ''
          apiNotification.innerHTML = notifications.success
          apiNotification.classList.remove('error')
          apiNotification.classList.add('show', 'success')
          //get category new lists
          getCategory();
          setTimeout(() => {
            apiNotification.classList.remove('show')
          }, 3000)

        }
      } else {
        hideLoad()
        console.log(err)
      }
    })
  }
  //btn delete category
  else if (e.target.classList.contains('option_yes_category')) {
    displayLoad()
    const id = +e.target.getAttribute('data-id')
    const data = `id=${id}&trigger=delete_category`
    const xml = new Xml();
    xml.postText('inc/dash_cat.php', data, (err, res) => {
      if (res) {
        let notifications = JSON.parse(res)
        if (notifications.error) {
          apiNotification.innerHTML = notifications.error
          apiNotification.classList.remove('success')
          apiNotification.classList.add('show', 'error')
          hideLoad()
          setTimeout(() => {
            apiNotification.classList.remove('show')
          }, 3000)
        } else {
          popUp.classList.remove('show')
          popupContainer.innerHTML = ''
          apiNotification.innerHTML = notifications.success
          apiNotification.classList.remove('error')
          apiNotification.classList.add('show', 'success')
          //get category new lists
          getCategory()
          setTimeout(() => {
            apiNotification.classList.remove('show')
          }, 3000)

        }
      } else {
        console.log(err)
      }
    })
  }
  //btn delete movie
  else if (e.target.classList.contains('option_yes_movie')) {
    displayLoad()
    const id = +e.target.getAttribute('data-id')
    const data = new FormData()
    data.append('trigger', 'delete_movie')
    data.append('id', id)
    const xml = new XmlFetch()
    xml.postFetch('inc/dash_insight.php', data)
      .then(res => {
        let notifications = JSON.parse(res)
        if (notifications.error) {
          apiNotification.innerHTML = notifications.error
          apiNotification.classList.remove('success')
          apiNotification.classList.add('show', 'error')
          hideLoad()
          setTimeout(() => {
            apiNotification.classList.remove('show')
          }, 3000)
        } else {
          popUp.classList.remove('show')
          popupContainer.innerHTML = ''
          apiNotification.innerHTML = notifications.success
          apiNotification.classList.remove('error')
          apiNotification.classList.add('show', 'success')
          //get movie new lists
          getMovies()
          setTimeout(() => {
            apiNotification.classList.remove('show')
          }, 3000)

        }

      })
      .catch(error => console.log(error))
  }
  //btn delete user
  else if (e.target.classList.contains('option_yes_user')) {
    // displayLoad()
    const id = +e.target.getAttribute('data-id')
    const data = new FormData()
    data.append('trigger', 'delete_user')
    data.append('id', id)
    const xml = new XmlFetch()
    xml.postFetch('inc/dash_members.php', data)
      .then(res => {
        let notifications = JSON.parse(res)
        if (notifications.error) {
          console.log(notifications.error)
          if (notifications.error === 'redirect') {
            window.location.href = 'dashboard.php'
          } else {
            apiNotification.innerHTML = notifications.error
            apiNotification.classList.remove('success')
            apiNotification.classList.add('show', 'error')
            hideLoad()
            setTimeout(() => {
              apiNotification.classList.remove('show')
            }, 3000)
          }
        } else {
          popUp.classList.remove('show')
          popupContainer.innerHTML = ''
          apiNotification.innerHTML = notifications.success
          apiNotification.classList.remove('error')
          apiNotification.classList.add('show', 'success')
          //remove row deleted
          const tr = `.target_${id}`
          document.querySelector(tr).remove()
          setTimeout(() => {
            apiNotification.classList.remove('show')
          }, 3000)

        }

      })
      .catch(error => console.log(error))
  }

})
//BUBBLING > TABLE Category
if (categoryTable !== null) {
  categoryTable.addEventListener('click', e => {
    e.preventDefault()
    // edit | delete link process
    if (e.target.classList.contains('edit_cat') || e.target.classList.contains('delete_cat')) {
      displayLoad()
      const name = e.target.className
      const id = +e.target.getAttribute('data-id')
      const xml = new Xml()
      xml.postText('inc/dash_cat.php', `trigger=cat_processing&id=${id}&name=${name}`, (err, res) => {
        if (res) {
          const data = JSON.parse(res)
          if (data.success) {
            const row = JSON.parse(data.success)
            if (data.btnTrigger === 'edit_cat') {
              //function paint the pop up PARAM[id | name]
              paintEditCategoryPopUp(row.id, row.category_name)
              popUp.classList.add('show')
              hideLoad()
            } else if (data.btnTrigger === 'delete_cat') {
              //function paint the pop up PARAM[id | name]
              paintConfirmDelete(row.id, row.category_name, 'option_yes_category')
              popUp.classList.add('show')
              hideLoad()
            }

          } else {
            apiNotification.innerHTML = data.error
            apiNotification.classList.remove('success')
            apiNotification.classList.add('show', 'error')
            hideLoad()
            setTimeout(() => {
              apiNotification.classList.remove('show')
            }, 3000)
          }
        } else {
          hideLoad()
          console.log(err)
        }
      })
    }
  })
}
//BUBBLING > TABLE movie 
if (movieTable !== null) {
  movieTable.addEventListener('click', e => {
    e.preventDefault()

    // status movie
    if (e.target.classList.contains('status_submit')) {
      displayLoad()
      const status = e.target.getAttribute('data-status')
      const id = e.target.getAttribute('data-id')

      let data = new FormData()
      data.append('status', status)
      data.append('id', id)
      data.append('trigger', 'update_status')
      const xml = new XmlFetch()
      xml.postFetch('inc/dash_insight.php', data)
        .then(res => {
          const data = JSON.parse(res)
          if (data.success) {
            // update btn
            const update_status = status === 'approved' ? 'pending' : 'approved'
            e.target.setAttribute('data-status', update_status)
            const textUpdate = e.target.textContent === 'approved' ? 'pending' : 'approved'
            e.target.className = `status_submit ${update_status}`
            e.target.textContent = textUpdate
            //update boxes numbers
            const assign = update_status === 'approved' ? 'plus' : 'minus'
            if (assign === 'plus') {
              publish_count.firstElementChild.lastElementChild.textContent = parseInt(publish_count.firstElementChild.lastElementChild.textContent) + 1
              pending_article_count.firstElementChild.lastElementChild.previousElementSibling.textContent = parseInt(pending_article_count.firstElementChild.lastElementChild.previousElementSibling.textContent) - 1
            } else {
              publish_count.firstElementChild.lastElementChild.textContent = parseInt(publish_count.firstElementChild.lastElementChild.textContent) - 1
              pending_article_count.firstElementChild.lastElementChild.previousElementSibling.textContent = parseInt(pending_article_count.firstElementChild.lastElementChild.previousElementSibling.textContent) + 1
            }
            apiNotification.innerHTML = data.success
            apiNotification.classList.remove('error')
            apiNotification.classList.add('show', 'success')
            hideLoad()
            setTimeout(() => {
              apiNotification.classList.remove('show')
            }, 3000)
          } else {
            apiNotification.innerHTML = data.error
            apiNotification.classList.remove('success')
            apiNotification.classList.add('show', 'error')
            hideLoad()
            setTimeout(() => {
              apiNotification.classList.remove('show')
            }, 3000)
          }

        })
        .catch(error => console.log(error))

    }
    // edit movie
    else if (e.target.parentElement.classList.contains('edit_movie')) {
      const id = parseInt(e.target.parentElement.getAttribute('data-id'));
      window.location.href = `dashboard.php?trigger=insight&edit_movie=${id}`
    }
    // delete link check
    else if (e.target.parentElement.classList.contains('delete_movie')) {
      displayLoad()
      const name = e.target.parentElement.getAttribute('data-value')
      const id = +e.target.parentElement.getAttribute('data-id')
      const xml = new XmlFetch()
      const data = new FormData();
      data.append('trigger', 'movie_check_process')
      data.append('movie_id', id)
      xml.postFetch('inc/dash_insight.php', data)
        .then(res => {
          const data = JSON.parse(res)
          if (data.success) {
            const row = JSON.parse(data.success)
            paintConfirmDelete(row.id, name, 'option_yes_movie')
            popUp.classList.add('show')
            hideLoad()
          } else {
            apiNotification.innerHTML = data.error
            apiNotification.classList.remove('success')
            apiNotification.classList.add('show', 'error')
            hideLoad()
            setTimeout(() => {
              apiNotification.classList.remove('show')
            }, 3000)
          }
        })
        .catch(error => console.log(error))
    }
    // redirect profile
    else if (e.target.classList.contains('profile_page')) {
      const profilePage = e.target.getAttribute('href')
      window.open(profilePage, '_blank')
    }
    // redirect movie
    else if (e.target.classList.contains('movie_site')) {
      const moviePage = e.target.getAttribute('href')
      window.open(moviePage, '_blank')
    }

  })
}
//BUBBLING > TABLE member
if (memberTable !== null) {
  memberTable.addEventListener('click', e => {
    e.preventDefault()
    // admin | writer
    if (e.target.classList.contains('permission_users')) {
      displayLoad()
      const id = +e.target.getAttribute('data-id')
      const data = new FormData()
      data.append('trigger', 'permission')
      data.append('id', id)
      const xml = new XmlFetch()
      xml.postFetch('inc/dash_members.php', data)
        .then(res => {
          let data = JSON.parse(res)
          if (data.success) {
            // ui update
            const switch_class = e.target.classList.contains('writer_btn') ? 'admin_btn' : 'writer_btn'
            e.target.classList.remove('writer_btn', 'admin_btn')
            e.target.classList.add(switch_class)
            const switch_txt = switch_class === 'writer_btn' ? 'writer' : 'admin'
            e.target.innerText = switch_txt
            //notification update
            apiNotification.innerHTML = data.success
            apiNotification.classList.remove('error')
            apiNotification.classList.add('show', 'success')
            hideLoad()
            setTimeout(() => {
              apiNotification.classList.remove('show')
            }, 3000)
          } else {
            if (data.error === 'redirect') {
              window.location.href = "dashboard.php";
            } else {
              apiNotification.innerHTML = data.error
              apiNotification.classList.remove('success')
              apiNotification.classList.add('show', 'error')
              hideLoad()
              setTimeout(() => {
                apiNotification.classList.remove('show')
              }, 3000)
            }
          }
        })
        .catch(error => console.log(error))
    }
    // status user
    else if (e.target.classList.contains('status_users')) {
      displayLoad()
      const id = e.target.getAttribute('data-id')
      let data = new FormData()
      data.append('id', id)
      data.append('trigger', 'status')
      const xml = new XmlFetch()
      xml.postFetch('inc/dash_members.php', data)
        .then(res => {
          const data = JSON.parse(res)
          if (data.success) {
            // ui update
            const switch_class = e.target.classList.contains('approved_btn') ? 'pending_btn' : 'approved_btn'
            e.target.classList.remove('approved_btn', 'pending_btn')
            e.target.classList.add(switch_class)
            e.target.innerText = data.status_txt
            //update boxes numbers
            const assign = data.status_txt === 'approved' ? 'plus' : 'minus'
            if (assign === 'plus') {
              approved_users_count.firstElementChild.lastElementChild.textContent = parseInt(approved_users_count.firstElementChild.lastElementChild.textContent) + 1
              pending_users_count.firstElementChild.lastElementChild.textContent = parseInt(pending_users_count.firstElementChild.lastElementChild.textContent) - 1
            } else {
              approved_users_count.firstElementChild.lastElementChild.textContent = parseInt(approved_users_count.firstElementChild.lastElementChild.textContent) - 1
              pending_users_count.firstElementChild.lastElementChild.textContent = parseInt(pending_users_count.firstElementChild.lastElementChild.textContent) + 1
            }
            apiNotification.innerHTML = data.success
            apiNotification.classList.remove('error')
            apiNotification.classList.add('show', 'success')
            hideLoad()
            setTimeout(() => {
              apiNotification.classList.remove('show')
            }, 3000)
          } else {
            if (data.error === 'redirect') {
              window.location.href = "dashboard.php";
            } else {
              apiNotification.innerHTML = data.error
              apiNotification.classList.remove('success')
              apiNotification.classList.add('show', 'error')
              hideLoad()
              setTimeout(() => {
                apiNotification.classList.remove('show')
              }, 3000)
            }
          }

        })
        .catch(error => console.log(error))

    }
    //trust lvl
    else if (e.target.classList.contains('trust_lvl')) {
      displayLoad()
      const id = e.target.parentElement.getAttribute('data-id')
      let data = new FormData()
      data.append('id', id)
      data.append('trigger', 'trust_lvl')
      const xml = new XmlFetch()
      xml.postFetch('inc/dash_members.php', data)
        .then(res => {
          const data = JSON.parse(res)
          if (data.success) {
            // ui update
            const switch_class = e.target.classList.contains('trust_ok') ? 'trust_normal' : 'trust_ok'
            e.target.classList.remove('trust_ok', 'trust_normal')
            e.target.classList.add(switch_class)

            apiNotification.innerHTML = data.success
            apiNotification.classList.remove('error')
            apiNotification.classList.add('show', 'success')
            hideLoad()
            setTimeout(() => {
              apiNotification.classList.remove('show')
            }, 3000)
          } else {
            if (data.error === 'redirect') {
              window.location.href = "dashboard.php";
            } else {
              apiNotification.innerHTML = data.error
              apiNotification.classList.remove('success')
              apiNotification.classList.add('show', 'error')
              hideLoad()
              setTimeout(() => {
                apiNotification.classList.remove('show')
              }, 3000)
            }
          }

        })
        .catch(error => console.log(error))
    }
    //delete lvl
    else if (e.target.classList.contains('delete_btn')) {
      // displayLoad()
      const id = e.target.parentElement.getAttribute('data-id')
      let data = new FormData()
      data.append('id', id)
      data.append('trigger', 'delete_check')
      const xml = new XmlFetch()
      xml.postFetch('inc/dash_members.php', data)
        .then(res => {
          const data = JSON.parse(res)
          console.log(data)
          if (data.success) {
            paintConfirmDelete(data.success.id, data.success.email, 'option_yes_user')
            popUp.classList.add('show')
            hideLoad()

          } else {

            if (data.error === 'redirect') {
              window.location.href = "dashboard.php";
            } else {
              apiNotification.innerHTML = data.error
              apiNotification.classList.remove('success')
              apiNotification.classList.add('show', 'error')
              hideLoad()
              setTimeout(() => {
                apiNotification.classList.remove('show')
              }, 3000)
            }
          }

        })
        .catch(error => console.log(error))
    }
    // redirect to profile
    else if (e.target.classList.contains('profile_page_table')) {
      const redirect = e.target.getAttribute('href')
      window.open(redirect, '_blank')
    }

  })
}

// Add Movie submit
if (movieAddForm !== null) {
  movieAddForm.addEventListener('submit', e => {
    displayLoad()
    e.preventDefault()
    const xml = new XmlFetch()
    const data = new FormData(movieAddForm)
    // https://www.tiny.cloud/blog/how-to-get-content-and-set-content-in-tinymce/
    let descriptionValue = tinymce.get("movie_description").getContent(); //fix bug 
    data.append('movie_description', descriptionValue)
    xml.postFetch('inc/dash_add.php', data)
      .then(data => {
        const notifications = JSON.parse(data)
        if (notifications.error) {
          apiNotification.innerHTML = notifications.error
          apiNotification.classList.remove('success')
          apiNotification.classList.add('show', 'error')
          hideLoad()
          setTimeout(() => {
            apiNotification.classList.remove('show')
          }, 3000)
        } else {
          // success and reload to main
          window.location.href = 'dashboard.php'
        }
      })
      .catch(error => console.log(error))
  })
}

// Edit Movie submit
if (movieEditForm !== null) {
  movieEditForm.addEventListener('submit', e => {
    displayLoad()
    e.preventDefault()
    const xml = new XmlFetch()
    const data = new FormData(movieEditForm)
    // https://www.tiny.cloud/blog/how-to-get-content-and-set-content-in-tinymce/
    let descriptionValue = tinymce.get("movie_description").getContent(); //fix bug 
    data.append('movie_description', descriptionValue)
    xml.postFetch('controller/movie_edit_process.php', data)
      .then(data => {
        console.log(data)
        const notifications = JSON.parse(data)
        if (notifications.error) {
          if (notifications.error === 'redirect') {
            window.location.href = 'dashboard.php'
          }
          else {
            apiNotification.innerHTML = notifications.error
            apiNotification.classList.remove('success')
            apiNotification.classList.add('show', 'error')
            hideLoad()
            setTimeout(() => {
              apiNotification.classList.remove('show')
            }, 3000)
          }
        } else {
          // success and reload to main
          window.location.href = 'dashboard.php'
        }
      })
      .catch(error => console.log(error))
  })
}
/*===============
FUNCTIONS
===============*/
// Add category function Popup
function paintAddCategoryPopUp() {
  popupContainer.innerHTML = ''
  let closeBtn = document.createElement('button')
  closeBtn.className = 'close'
  closeBtn.innerHTML = '<i class="fas fa-times"></i>'
  let h3Elm = document.createElement('h3')
  h3Elm.innerText = 'add category'

  let form = document.createElement('form')
  form.innerHTML = "<input type='text' placeholder='add category'>"
  form.innerHTML += "<input class='add_category' type='submit' value='add'>"
  popupContainer.append(closeBtn)
  popupContainer.append(h3Elm)
  popupContainer.append(form)
}
// Edit category function Popup
function paintEditCategoryPopUp(id, value) {
  popupContainer.innerHTML = ''
  let closeBtn = document.createElement('button')
  closeBtn.className = 'close'
  closeBtn.innerHTML = '<i class="fas fa-times"></i>'
  let h3Elm = document.createElement('h3')
  h3Elm.innerText = `edit category "${value}"`

  let form = document.createElement('form')
  form.innerHTML = `<input type='hidden' value='${id}'>`
  form.innerHTML += `<input type='text' value='${value}' placeholder='edit category'>`
  form.innerHTML += "<input class='edit_category' type='submit' value='edit'>"
  popupContainer.append(closeBtn)
  popupContainer.append(h3Elm)
  popupContainer.append(form)
}
//Delete category function Popup
function paintConfirmDelete(id, value, classBtnYes) {
  popupContainer.innerHTML = ''
  let closeBtn = document.createElement('button')
  closeBtn.className = 'close'
  closeBtn.innerHTML = '<i class="fas fa-times"></i>'
  let h3Elm = document.createElement('h3')
  h3Elm.innerText = `Confirm delete: "${value}"`
  let yesBtn = document.createElement('a')
  yesBtn.className = `option_yes ${classBtnYes}`
  yesBtn.setAttribute('href', '#')
  yesBtn.setAttribute('data-id', id)
  yesBtn.innerText = 'yes'
  let noBtn = document.createElement('a')
  noBtn.className = 'option_no'
  noBtn.setAttribute('href', '#')
  noBtn.innerText = 'no'
  popupContainer.append(closeBtn)
  popupContainer.append(h3Elm)
  popupContainer.append(yesBtn)
  popupContainer.append(noBtn)

}
// add table row category
function paintTableRow(id, name) {
  let tbody = categoryTable.lastElementChild
  let tr = document.createElement('tr')
  tr.innerHTML = `<td>${name}</td>
                      <td><a class="edit_cat" data-id='${id}' href="">edit</a></td>
                      <td><a class="delete_cat" data-id='${id}'href="">delete</a></td>
                    `
  tbody.insertAdjacentElement('afterbegin', tr)

}
// get categories table
function getCategory() {
  let tbody = categoryTable.lastElementChild
  tbody.innerHTML = ''
  const xml = new Xml()
  xml.get('controller/get_category.php', (err, res) => {
    if (res) {
      let data = JSON.parse(res)
      if (data.success) {
        data.success.forEach(item => { paintTableRow(item['id'], item['category_name']) })
        categoryTable.classList.add('show')
        dashboardNotice.classList.remove('show')
        searchCategoryInput.classList.add('show')
        searchCategoryInput.nextElementSibling.classList.add('show')
        hideLoad()
      }
      else {
        searchCategoryInput.classList.remove('show')
        searchCategoryInput.nextElementSibling.classList.remove('show')
        categoryTable.classList.remove('show')
        dashboardNotice.firstElementChild.innerText = data.error
        dashboardNotice.classList.add('show')
        hideLoad()
      }
    } else {
      hideLoad()
      console.log(err)
    }
  })
}
// get movies table
function getMovies() {
  let tbody = movieTable.lastElementChild
  tbody.innerHTML = ''
  const xml = new XmlFetch()
  xml.getFetch('controller/get_movie.php')
    .then(res => {
      let data = JSON.parse(res)
      if (data.success) {
        // get tables data
        data.success.get_all_movies.forEach(item => {

          paintTableRowMovies(item.movie_id,
            item.movie_or_series,
            item.movie_name,
            item.poster,
            item.category_id,
            item.category_name,
            item.likes,
            item.movie_status,
            item.user_id,
            item.user_name,
            item.views,
            item.year,
            data.success.permission)
        })
        // get insight boxes
        updateInsightBoxes(
          data.success.view_count,
          data.success.publish_count,
          data.success.comment_count,
          data.success.user_count,
          data.success.pending_article_count,
          data.success.pending_comment_count,
          data.success.permission)

        movieTable.classList.add('show')
        dashboardNotice.classList.remove('show')
        // searchMovieInput.classList.add('show')
        // searchMovieInput.nextElementSibling.classList.add('show')
        hideLoad()
      } else {
        // searchMovieInput.classList.remove('show')
        // searchMovieInput.nextElementSibling.classList.remove('show')
        movieTable.classList.remove('show')
        dashboardNotice.firstElementChild.innerText = data.error
        dashboardNotice.classList.add('show')
        hideLoad()
      }
    })
    .catch(error => console.log(error))
}
// add table row movies
function paintTableRowMovies(movie_id, movie_or_series, movie_name, poster, category_id, category_name, likes, movie_status, user_id, user_name, views, year, permission) {
  let status = (+movie_status === 1 ? 'pending' :
    (+movie_status === 2 ? 'approved' :
      (+movie_status === 3 ? 'refused' :
        ''
      )))
  let td_user = user_name === null ? '' : `<td><a class="profile_page" href="./profile.php?profile=${user_id}" target="_blank">${user_name}</a></td>`
  let status_permission = permission === 'admin' ?
    `<td class='btn_status'><a data-status='${status}' data-id='${movie_id}' class='status_submit ${status}' href='#'>${status}</a></td>` :
    `<td>${status}</td>`
  let vote = JSON.parse(likes)
  let tbody = movieTable.lastElementChild
  let tr = document.createElement('tr')
  tr.innerHTML = `         
                  <td>
                        <figure class="poster-img">
                          <img src="./${poster}" alt="${movie_name}">
                        </figure>
                  </td>
                  <td><a class="movie_site" target="_blank" href="./index.php?target=movies&id=${movie_id}">${movie_name}</a></td>
                  <td>${movie_or_series}</td>
                  <td>${year}</td>
                  <td>${category_name}</td>
                  ${td_user}
                  <td><i class="fas fa-thumbs-up"></i> ${vote.like}</td>
                  <td><i class="fas fa-thumbs-down"></i> ${vote.dislike}</td>
                  <td>${views}</td>
                  ${status_permission}
                  <td><a class="edit_movie" data-id="${movie_id}" href="#"><i class="fas fa-edit"></i></a></td>
                  <td><a class="delete_movie" data-value="${movie_name}" data-id="${movie_id}" href="#"><i class="fas fa-trash-alt"></i></a></td>
                    `
  tbody.append(tr)
}

// search query dashboard
function searchQuery(inputSearch) {
  inputSearch.addEventListener('input', function (e) {
    const value = e.target.value.toLowerCase().trim()
    console.log(value)
    const data = `name=${value}&trigger=search_category`
    const xml = new Xml()
    xml.postText('inc/dash_cat.php', data, (err, res) => {
      if (res) {
        let notifications = JSON.parse(res)
        let tbody = categoryTable.lastElementChild
        tbody.innerHTML = ''
        if (notifications.error) {
          apiNotification.innerHTML = notifications.error
          apiNotification.classList.remove('success')
          apiNotification.classList.add('show', 'error')
          setTimeout(() => {
            apiNotification.classList.remove('show')
          }, 3000)
        } else {
          // add search result
          notifications.success.forEach(item => {
            paintTableRow(item.id, item.category_name)
          })
        }
      } else {
        console.log(err)
      }
    })

  })
}
// Insight boxes
function updateInsightBoxes(view, publish, comment, user, pendig_movies, pending_comments, permission) {

  view_count.firstElementChild.lastElementChild.textContent = view
  publish_count.firstElementChild.lastElementChild.textContent = publish
  comment_count.firstElementChild.lastElementChild.textContent = comment
  if (permission === 'admin') {
    user_count.firstElementChild.lastElementChild.textContent = user
    pending_article_count.firstElementChild.lastElementChild.previousElementSibling.textContent = pendig_movies
    pending_comment_count.firstElementChild.lastElementChild.previousElementSibling.textContent = pending_comments
  }


}