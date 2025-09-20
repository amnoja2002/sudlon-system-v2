<div class="contact-section">
    <div class="grid md:grid-cols-2 gap-8">
        <!-- Contact Form -->
        <div class="rounded-xl bg-white shadow-lg border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h2 class="font-display text-2xl font-bold text-deped-700">Contact Information</h2>
                <p class="text-gray-600 mt-2">Send us a message and we'll get back to you as soon as possible.</p>
            </div>
            <form wire:submit.prevent="submit" class="p-6 space-y-4">
            @if (session('message'))
                <div class="mb-3 rounded-lg bg-green-50 text-green-800 px-4 py-2 border border-green-200">
                    {{ session('message') }}
                </div>
            @endif
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="name">
                    Full Name
                </label>
                <input 
                    wire:model="name"
                    type="text" 
                    id="name"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:ring-2 focus:ring-deped-500 focus:border-deped-500" 
                    placeholder="Juan Dela Cruz" 
                />
                @error('name') 
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="email">
                    Email Address
                </label>
                <input 
                    wire:model="email"
                    type="email" 
                    id="email"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:ring-2 focus:ring-deped-500 focus:border-deped-500" 
                    placeholder="juan.delacruz@example.com" 
                />
                @error('email') 
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="message">
                    Your Message
                </label>
                <textarea 
                    wire:model="message"
                    id="message"
                    rows="4" 
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:ring-2 focus:ring-deped-500 focus:border-deped-500" 
                    placeholder="How can we assist you today?"
                ></textarea>
                @error('message') 
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end pt-2">
                <button type="submit" class="inline-flex items-center justify-center rounded-lg border border-black bg-white text-black px-5 py-2.5 font-medium hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2 active:scale-[.98] transition-all">
                    <span wire:loading.class="hidden" wire:target="submit">
                        <i class="fa-solid fa-paper-plane mr-2 -ml-1"></i>
                        Send Message
                    </span>
                    <span class="hidden" wire:loading.class.remove="hidden" wire:target="submit">
                        <i class="fa-solid fa-circle-notch fa-spin mr-2 -ml-1"></i>
                        Sending...
                    </span>
                </button>
            </div>
        </form>
    </div>

    <!-- Contact Details -->
    <div class="space-y-6">
        <div class="rounded-xl bg-deped-50 p-6 border border-deped-100">
            <h3 class="font-display text-lg font-semibold text-deped-800">School Address</h3>
            <div class="mt-4 space-y-3 text-gray-600">
                <div class="flex items-start">
                    <i class="fa-solid fa-location-dot text-deped-600 mt-0.5 mr-3 flex-shrink-0"></i>
                    <span>Sudlon Elementary School<br>San Vicente, Surigao del Sur<br>Philippines 8317</span>
                </div>
            </div>
        </div>

        <div class="rounded-xl bg-deped-50 p-6 border border-deped-100">
            <h3 class="font-display text-lg font-semibold text-deped-800">Contact Details</h3>
            <div class="mt-4 space-y-3 text-gray-600">
                <div class="flex items-center">
                    <i class="fa-solid fa-envelope text-deped-600 mr-3 flex-shrink-0"></i>
                    <a href="mailto:sudlones.sanvicente@deped.gov.ph" class="hover:text-deped-600">sudlones.sanvicente@deped.gov.ph</a>
                </div>
                <div class="flex items-center">
                    <i class="fa-solid fa-phone text-deped-600 mr-3 flex-shrink-0"></i>
                    <span>(032) 123-4567</span>
                </div>
                <div class="flex items-center">
                    <i class="fa-solid fa-clock text-deped-600 mr-3 flex-shrink-0"></i>
                    <span>Monday - Friday: 7:00 AM - 5:00 PM</span>
                </div>
            </div>
        </div>

        <div class="rounded-xl bg-deped-50 p-6 border border-deped-100">
            <h3 class="font-display text-lg font-semibold text-deped-800">Connect With Us</h3>
            <div class="mt-4 flex items-center space-x-4">
                <a href="#facebook" class="text-deped-600 hover:text-deped-700">
                    <span class="sr-only">Facebook</span>
                    <i class="fa-brands fa-facebook fa-lg"></i>
                </a>
                <a href="#youtube" class="text-deped-600 hover:text-deped-700">
                    <span class="sr-only">YouTube</span>
                    <i class="fa-brands fa-youtube fa-lg"></i>
                </a>
            </div>
        </div>
    </div>
</div>
    </div>
</div>