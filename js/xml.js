// XML --> used in users | category
function Xml() {
  this.xml = new XMLHttpRequest();
}

Xml.prototype = {

  get: function (url, callBack) {
    let that = this.xml
    that.open('GET', url, true)
    that.onprogress = () => { console.log(that.readyState) }
    that.onload = () => {
      if (that.status === 200)
        callBack(null, that.responseText)
      else
        callBack('ERR:1')
    }
    that.onerror = () => { console.log('ERR:2') }
    that.send()
  },
  // no files | when click i didnt use it for submit
  postText: function (url, data, callBack) {
    let that = this.xml
    that.open("POST", url, true)
    that.setRequestHeader('content-type', 'application/x-www-form-urlencoded')
    that.onprogress = () => console.log(that.readyState)
    that.onload = () => {
      if (that.status === 200)
        callBack(null, that.responseText)
      else
        callBack('ERR:1')
    }
    that.onerror = () => console.log('ERR:2')
    that.send(data)
  },
  // can allow files and submit
  post: function (url, data, callBack) {
    let that = this.xml
    that.open("POST", url, true)
    that.onprogress = () => console.log(that.readyState)
    that.onload = () => {
      if (that.status === 200)
        callBack(null, that.responseText)
      else
        callBack('ERR:1')
    }
    that.onerror = () => console.log('ERR:2')
    that.send(data)
  }
}

class XmlFetch {
  // fetch used in movies
  getFetch(url) {
    return new Promise((resolve, reject) => {
      fetch(url)
        .then(res => res.text())
        .then(data => resolve(data))
        .catch(error => reject(error))
    })
  }

  postFetch(url, data) {
    return new Promise((resolve, reject) => {
      fetch(url, {
        method: 'POST',
        body: data
      })
        .then(res => res.text())
        .then(d => resolve(d))
        .catch(error => reject(error))
    })
  }
}
