<template>

  <div class="chat-wrapper">
  
    <div class="chat-header">
      <img src="/src/assets/logo.png" class="chat-logo"/>
      <span>Mentor Dualab</span>
    </div>
  
    <div class="chat-box" ref="chatBox">
  
      <div
        v-for="(msg,index) in messages"
        :key="index"
        :class="msg.role === 'Alumno' ? 'msg user' : 'msg bot'"
      >
        {{ msg.text }}
      </div>
  
      <!-- QUICK REPLIES DENTRO DEL CHAT -->
  
      <div v-if="messages.length === 1" class="suggestions">
  
        <button
          v-for="(q,index) in suggestions"
          :key="index"
          class="suggestion-btn"
          @click="sendSuggestion(q)"
        >
          {{ q }}
        </button>
  
      </div>
  
    </div>
  
    <div class="chat-input">
      <input
        v-model="input"
        placeholder="Escribe tu pregunta..."
        @keyup.enter="sendMessage"
      />
      <button @click="sendMessage">
        Enviar
      </button>
    </div>
  
  </div>
  
  </template>
  
  <script setup>

  import "../assets/css/chat.css"
  import { ref, nextTick, onMounted } from "vue"
  
  const input = ref("")
  const messages = ref([])
  const chatBox = ref(null)
  

  
  /* PREGUNTAS SUGERIDAS */
  
  const suggestions = [
    "¿Cuál es el horario de prácticas?",
    "¿Quién es mi tutor?",
    "¿Cuántas horas debo hacer?",
    "¿Cómo justifico una ausencia?"
  ]
  
  function sendSuggestion(text){
    input.value = text
    sendMessage()
  }
  
  /* MENSAJE INICIAL */
  
  onMounted(() => {
  
    messages.value.push({
      role:"Mentor",
      text:"Hola 👋 Soy el mentor virtual. ¿En qué puedo ayudarte?"
    })
  
  })
  
  async function sendMessage(){
  
    if(!input.value) return
  
    messages.value.push({
      role:"Alumno",
      text:input.value
    })
  
    const question = input.value
    input.value=""
  
    try{
  
      const res = await fetch("http://localhost:8000/api/mentor",{
        method:"POST",
        headers:{
          "Content-Type":"application/json"
        },
        body:JSON.stringify({
          message:question
        })
      })
  
      const data = await res.json()
  
      messages.value.push({
        role:"Mentor",
        text:data.choices?.[0]?.message?.content || data.message || "Sin respuesta"
      })
  
    }catch{
  
      messages.value.push({
        role:"Mentor",
        text:"El mentor no está disponible ahora."
      })
  
    }
  
    await nextTick()
  
    chatBox.value.scrollTop = chatBox.value.scrollHeight
  
  }
  
  </script>
  
 