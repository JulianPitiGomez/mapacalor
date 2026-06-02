<!-- resources/views/livewire/reloj.blade.php -->
<div class="h-screen flex flex-col p-1 gap-0.5">

    <!-- Fila de Cámara, Reloj y Captura -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 shrink-0">
        <!-- Cámara en Vivo -->
        <div class="bg-white rounded-2xl p-2 shadow-lg border border-primary-green/10 flex flex-col">
            <div class="flex items-center justify-center gap-2 mb-1 pb-0.5 border-b-2 border-secondary-light text-sm font-semibold text-gray-800">
                <i class="bi bi-camera-video-fill text-lg text-primary-green"></i>
                <span>Cámara en Vivo</span>
            </div>
            <div class="flex-1 min-h-0">
                <video 
                    autoplay="true" 
                    id="videoElement"
                    class="w-full h-full object-cover rounded-xl border-2 border-secondary-green gradient-canvas-bg"
                ></video>
            </div>
        </div>

        <!-- Reloj Central -->
        <div class="bg-white rounded-2xl p-2 shadow-lg border border-primary-green/10 flex flex-col items-center justify-center">
            <div class="text-center mb-4 px-4 py-2 bg-secondary-light/50 rounded-full">
                <div class="flex items-center justify-center gap-2 text-sm md:text-base text-gray-700">
                    <i class="bi bi-credit-card text-primary-green text-lg"></i>
                    <span class="font-medium">Pase la tarjeta por el lector</span>
                </div>
            </div>
            <div class="gradient-green rounded-3xl px-8 py-6 text-center text-white shadow-2xl shadow-primary-green/30 w-full">
                <div id="fecha-display" class="text-sm md:text-base opacity-90 mb-2 uppercase tracking-widest font-medium">
                    {{ $fecha }}
                </div>
                <div id="reloj" class="text-4xl md:text-5xl lg:text-6xl font-bold tracking-wider font-mono drop-shadow-lg">
                    00:00:00
                </div>
            </div>
        </div>

        <!-- Última Captura -->
        <div class="bg-white rounded-2xl p-2 shadow-lg border border-primary-green/10 flex flex-col">
            <div class="flex items-center justify-center gap-2 mb-1 pb-0.5 border-b-2 border-secondary-light text-sm font-semibold text-gray-800">
                <i class="bi bi-camera-fill text-lg text-primary-green"></i>
                <span>Última Captura</span>
            </div>
            <div wire:ignore class="flex-1 min-h-0">
                <canvas 
                    id="micanvas"
                    class="w-full h-full object-cover rounded-xl border-2 border-secondary-green gradient-canvas-bg"
                ></canvas>
            </div>
        </div>
    </div>
    
    <!-- Input de Tarjeta (OCULTO pero funcional) -->
    <input 
        type="text" 
        class="absolute opacity-0 pointer-events-none" 
        wire:model="tarjeta"
        id="tarjeta" 
        autofocus 
        autocomplete="off"
        tabindex="-1">
    
    <!-- Área de Resultado Centrado (Entre Reloj y Marquesina) -->
    <div class="flex-1 flex items-center justify-center p-2">
        @if($mostrarResultado)
            <div class="bg-white rounded-2xl p-2 shadow-2xl border-4 border-primary-green/20 max-w-4xl w-full animate-slide-in-up">
                {!! $resultado !!}
            </div>
        @endif
    </div>
    
    <!-- Marquesina con Notificaciones (Inferior) -->
    @if($notificaciones->count() > 0)
    <div class="shrink-0 px-1">
        <div class="news-ticker gradient-dark rounded-2xl p-0.5 shadow-lg overflow-hidden relative">
            <marquee scrollamount="8" class="py-0.5">
                @foreach($notificaciones as $noticia)
                    <span class="inline-block mx-12">
                        <span class="text-secondary-green font-semibold text-base md:text-lg">
                            <i class="bi bi-megaphone-fill"></i> {{ $noticia->TITULO }}:
                        </span>
                        <span class="text-accent-pink text-sm md:text-base ml-2">
                            {{ strip_tags($noticia->DETALLE) }}
                        </span>
                        <span class="text-white/50 mx-4 text-base">•</span>
                    </span>
                @endforeach
            </marquee>
        </div>
    </div>
    @endif

@script
<script>
    let tiempoFoto = null;
    let tiempoResultado = null;
    const video = document.querySelector("#videoElement");
    const tarjetaInput = document.getElementById("tarjeta");
    
    // Inicializar cámara
    if (navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(function (stream) {
                video.srcObject = stream;
            })
            .catch(function (err) {
                console.error("Error al acceder a la cámara:", err);
            });
    }
    
    // Función para mantener foco y selección
    function mantenerFoco() {
        if (document.activeElement !== tarjetaInput) {
            tarjetaInput.focus();
        }
    }
    
    // Mantener foco constantemente
    setInterval(mantenerFoco, 500);
    
    // Capturar foto y fichar cuando se presiona Enter
    tarjetaInput.addEventListener('keypress', function(event) {
        if (event.keyCode === 13 || event.which === 13) {
            event.preventDefault();
            capturarYFichar();
        }
    });
    
    // Prevenir pérdida de foco
    tarjetaInput.addEventListener('blur', function() {
        setTimeout(() => {
            tarjetaInput.focus();
        }, 10);
    });
    
    // Capturar clics en cualquier parte y devolver foco
    document.addEventListener('click', function(e) {
        setTimeout(() => {
            tarjetaInput.focus();
        }, 10);
    });
    
    async function capturarYFichar() {
        const canvas = document.getElementById('micanvas');
        
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        
        const context = canvas.getContext("2d");
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        // Limpiar timeouts anteriores si existen
        if (tiempoFoto) clearTimeout(tiempoFoto);
        if (tiempoResultado) clearTimeout(tiempoResultado);
        
        // Programar limpieza de foto después de 10 segundos
        tiempoFoto = setTimeout(() => {
            const canvas = document.getElementById("micanvas");
            const context = canvas.getContext("2d");
            context.clearRect(0, 0, canvas.width, canvas.height);
            tiempoFoto = null;
        }, 10000);
        
        // Obtener foto en base64
        const fotoBase64 = canvas.toDataURL('image/jpeg', 0.8);
        
        // IMPORTANTE: Esperar a que Livewire procese la respuesta
        try {
            await $wire.fichar(fotoBase64);
            
            // Programar limpieza de resultado después de 10 segundos
            tiempoResultado = setTimeout(() => {
                $wire.limpiarResultado();
                tiempoResultado = null;
            }, 10000);
        } catch (error) {
            console.error("Error al fichar:", error);
        }
        
        // Devolver foco inmediatamente
        setTimeout(() => {
            tarjetaInput.focus();
        }, 100);
    }
    
    // Reloj en tiempo real
    function actualizarReloj() {
        const now = new Date();
        
        const horas = String(now.getHours()).padStart(2, '0');
        const minutos = String(now.getMinutes()).padStart(2, '0');
        const segundos = String(now.getSeconds()).padStart(2, '0');
        
        document.getElementById('reloj').textContent = horas + ':' + minutos + ':' + segundos;
        
        setTimeout(actualizarReloj, 500);
    }
    
    // Iniciar reloj
    window.onload = function() {
        actualizarReloj();
        tarjetaInput.focus();
    };
    
    // AUTO-REFRESH: Recargar página cada 1 hora (3600000 ms)
    setInterval(() => {
        console.log('Auto-refresh activado - recargando página...');
        location.reload(true);
    }, 3600000); // 1 hora = 60 minutos * 60 segundos * 1000 ms
    
    // Escuchar evento de Livewire para limpiar
    Livewire.on('limpiarResultado', () => {
        if (tiempoFoto) {
            clearTimeout(tiempoFoto);
            tiempoFoto = null;
        }
        if (tiempoResultado) {
            clearTimeout(tiempoResultado);
            tiempoResultado = null;
        }
    });
</script>
@endscript
</div>