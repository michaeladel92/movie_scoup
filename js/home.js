const actions = document.querySelector('.actions'),
  notifications = document.querySelector('.notifications_global_api'),
  searchMoviesInput = document.getElementById('search_movies'),
  movieListsContainer = document.querySelector('.movie_lists')

if (actions !== null) {
  actions.addEventListener('click', e => {
    e.preventDefault()
    if (e.target.classList.contains('fa-thumbs-up')) {

      const movie = e.target.parentElement.getAttribute('data-movie');
      const xml = new XmlFetch()
      const data = new FormData()
      data.append('trigger', 'liked')
      data.append('id', movie)
      xml.postFetch('controller/actions.php', data)
        .then(res => {
          const response = JSON.parse(res)
          if (response.error) {
            notifications.innerHTML = response.error
            notifications.classList.remove('success')
            notifications.classList.add('show', 'error')
            setTimeout(() => {
              notifications.classList.remove('show')
            }, 3000)
          } else {
            //thumbs up 
            const switching = e.target.parentElement.classList.contains('default_like') ? 'like' : 'default_like'
            e.target.parentElement.className = switching
            // total like
            const likeNumber = switching === 'like' ?
              +e.target.parentElement.nextElementSibling.innerText + 1 :
              +e.target.parentElement.nextElementSibling.innerText - 1
            e.target.parentElement.nextElementSibling.innerText = likeNumber
            // thumbs down 
            const checkDislike = e.target.parentElement.parentElement.nextElementSibling.firstElementChild.classList.contains('dislike') ? 'default_dislike' : ''
            if (checkDislike !== '') {
              e.target.parentElement.parentElement.nextElementSibling.firstElementChild.className = checkDislike
              // total number
              e.target.parentElement.parentElement.nextElementSibling.lastElementChild.innerText =
                +e.target.parentElement.parentElement.nextElementSibling.lastElementChild.innerText - 1


            }
          }
        })
        .catch(error => console.log(error))
    }
    if (e.target.classList.contains('fa-thumbs-down')) {

      const movie = e.target.parentElement.getAttribute('data-movie');
      const xml = new XmlFetch()
      const data = new FormData()
      data.append('trigger', 'disLiked')
      data.append('id', movie)
      xml.postFetch('controller/actions.php', data)
        .then(res => {
          console.log(res)
          const response = JSON.parse(res)
          if (response.error) {
            notifications.innerHTML = response.error
            notifications.classList.remove('success')
            notifications.classList.add('show', 'error')
            setTimeout(() => {
              notifications.classList.remove('show')
            }, 3000)
          } else {
            //thumbs down 
            const switching = e.target.parentElement.classList.contains('default_dislike') ? 'dislike' : 'default_dislike'
            e.target.parentElement.className = switching
            // total dislike
            const disLikeNumber = switching === 'dislike' ?
              +e.target.parentElement.nextElementSibling.innerText + 1 :
              +e.target.parentElement.nextElementSibling.innerText - 1
            e.target.parentElement.nextElementSibling.innerText = disLikeNumber
            // thumbs up 
            const checkLike = e.target.parentElement.parentElement.previousElementSibling.firstElementChild.classList.contains('like') ? 'default_like' : ''
            if (checkLike !== '') {
              e.target.parentElement.parentElement.previousElementSibling.firstElementChild.className = checkLike
              // total number
              e.target.parentElement.parentElement.previousElementSibling.lastElementChild.innerText =
                +e.target.parentElement.parentElement.previousElementSibling.lastElementChild.innerText - 1
            }
          }
        })
        .catch(error => console.log(error))
    }
  })
}

if (searchMoviesInput !== null) {
  searchMoviesInput.addEventListener('input', e => {
    const searchQuery = e.target.value
    const xml = new XmlFetch()
    const data = new FormData()
    data.append('trigger', 'searchQuery')
    data.append('search', searchQuery)
    xml.postFetch('inc/home.php', data)
      .then(res => {
        console.log(res)
        const data = JSON.parse(res)
        if (data.success) {
          movieListsContainer.innerHTML = ''
          let boxes = ''
          data.success.forEach(item => {
            boxes += paintMovieBoxes(
              item.id,
              item.movie_name,
              item.poster,
              item.trust_lvl,
              item.user_id_,
              item.user_name,
              item.views
            )
          })
          movieListsContainer.innerHTML = boxes
          notifications.innerText = 'Query found!'
          notifications.classList.remove('show', 'error')
          notifications.classList.add('show', 'success')
          setTimeout(() => {
            notifications.classList.remove('show', 'success')
          }, 3000)

        } else {
          movieListsContainer.innerHTML = ''
          notifications.innerText = data.error
          notifications.classList.remove('success')
          notifications.classList.add('show', 'error')
          setTimeout(() => {
            notifications.classList.remove('show', 'error')
          }, 3000)
        }
      })
      .catch(error => console.log(error))
  })
}


function paintMovieBoxes(id, movie_name, poster, trust_lvl, user_id, user_name, views) {
  const trust = +trust_lvl === 1 ? '<i class="fas fa-check-circle trust_ok"></i>' : ''
  const box = `
            <div class="boxes">
              <figure>
                <a target="_blank" href="./index.php?target=movies&id=${id}">
                    <img src="./${poster}" alt="${movie_name}">
                </a>
              </figure>
              <figcaption>
                <small>view: ${views}</small>
                <h3><a target="_blank" href="./index.php?target=movies&id=${id}">${movie_name}</a></h3>
                <h4>by: <a target="_blank" href="./profile.php?profile=${user_id}">${user_name}</a> 
                      ${trust}
                    </h4>
              </figcaption>
            </div>`

  return box

}
