<!-- Delete Student Confirmation Modal -->
<div x-data="{ show: @entangle('showingDeleteStudentModal') }"
     x-show="show"
     x-cloak
     class="fixed inset-0 z-[80]">
    <div class="absolute inset-0 bg-black/50" @click="$wire.call('cancelDeleteStudent')"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div x-show="show" class="relative z-10 w-full max-w-md mx-auto bg-white rounded-lg text-left overflow-hidden shadow-xl">
            <div class="px-6 pt-5 pb-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Confirm Deletion</h3>
                <p class="text-sm text-gray-600">Are you sure you want to delete this student? This action cannot be undone.</p>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button"
                        wire:click="confirmDeleteStudent"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Delete
                </button>
                <button type="button"
                        wire:click="cancelDeleteStudent"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

