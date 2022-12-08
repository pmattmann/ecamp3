import demo from './index.js'

import './assets/main.css'
;(async () => {
  const blob = await demo()
  document.getElementById('pdf-iframe').src =
    URL.createObjectURL(blob) + '#navpanes=0&pagemode=none&zoom=page-fit'
})()
