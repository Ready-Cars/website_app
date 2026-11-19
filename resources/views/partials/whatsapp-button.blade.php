@php
    $whatsappNumber = '2349022072949';
    $whatsappMessage = rawurlencode('Hello ReadyCars, I would like to make an enquiry.');
@endphp

<a href="https://wa.me/{{ $whatsappNumber }}?text={{ $whatsappMessage }}"
   target="_blank"
   rel="noopener noreferrer"
   aria-label="Talk to us on WhatsApp (+234 902 207 2949)"
   title="Talk to us on WhatsApp"
   onclick="if (typeof gtag === 'function') { gtag('event', 'whatsapp_click', { event_category: 'contact', event_label: 'floating_button' }); }"
   class="group fixed bottom-5 right-5 z-50 flex items-center rounded-full bg-[#25D366] p-3.5 text-white sm:p-4 shadow-lg shadow-black/20 transition-all duration-300 hover:bg-[#1ebe5b] hover:shadow-xl focus:outline-none focus-visible:ring-4 focus-visible:ring-[#25D366]/40 sm:bottom-6 sm:right-6">
    <span class="absolute inset-0 -z-10 animate-ping rounded-full bg-[#25D366] opacity-30 group-hover:hidden motion-reduce:hidden"></span>
    <svg class="h-9 w-9 shrink-0 sm:h-10 sm:w-10" viewBox="0 0 32 32" fill="currentColor" aria-hidden="true">
        <path d="M16.004 3C8.837 3 3.004 8.832 3.004 16c0 2.29.6 4.528 1.74 6.5L3 29l6.664-1.716A12.95 12.95 0 0 0 16.004 29C23.17 29 29 23.168 29 16S23.17 3 16.004 3Zm0 23.636a10.6 10.6 0 0 1-5.41-1.48l-.388-.23-3.955 1.018 1.057-3.855-.253-.397A10.58 10.58 0 0 1 5.37 16c0-5.864 4.77-10.636 10.634-10.636S26.636 10.136 26.636 16 21.866 26.636 16.004 26.636Zm5.832-7.964c-.32-.16-1.89-.932-2.183-1.038-.293-.107-.506-.16-.719.16-.213.32-.826 1.038-1.012 1.252-.187.213-.373.24-.693.08-.32-.16-1.35-.498-2.571-1.588-.95-.848-1.592-1.895-1.778-2.215-.187-.32-.02-.493.14-.652.144-.143.32-.373.48-.56.16-.186.213-.32.32-.533.106-.213.053-.4-.027-.56-.08-.16-.719-1.733-.985-2.373-.26-.623-.523-.539-.719-.549l-.613-.01a1.177 1.177 0 0 0-.852.4c-.293.32-1.118 1.092-1.118 2.665s1.145 3.092 1.305 3.305c.16.213 2.253 3.44 5.458 4.825.763.33 1.358.526 1.822.673.765.244 1.462.21 2.012.127.614-.091 1.89-.773 2.157-1.519.266-.746.266-1.386.186-1.519-.08-.133-.293-.213-.613-.373Z"/>
    </svg>
    <span class="max-w-0 overflow-hidden whitespace-nowrap text-base font-semibold opacity-0 transition-all duration-300 group-hover:ml-2 group-hover:mr-2 group-hover:max-w-xs group-hover:opacity-100 group-focus-visible:ml-2 group-focus-visible:mr-2 group-focus-visible:max-w-xs group-focus-visible:opacity-100">Talk to us on WhatsApp</span>
</a>
