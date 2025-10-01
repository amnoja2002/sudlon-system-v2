<!-- Report Scope Modal -->
<div x-data="{ show: @entangle('showingReportScopeModal') }"
     x-show="show"
     x-cloak
     class="fixed inset-0 z-50"
     x-transition>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div x-show="show" class="fixed inset-0 bg-transparent"></div>

        <div x-show="show" class="relative z-10 w-full max-w-lg mx-auto bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all" x-transition>
            <form wire:submit.prevent="confirmReportScope">
                <div class="bg-white px-6 pt-5 pb-4 sm:p-8 sm:pb-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">
                        Select Report Scope
                    </h3>

                    <div class="space-y-4">
                        <label class="flex items-start space-x-3 p-3 border rounded-md cursor-pointer hover:bg-gray-50">
                            <input type="radio" class="mt-1" value="mine" wire:model="reportScope">
                            <div>
                                <div class="text-sm font-medium text-gray-900">Generate for my subjects only</div>
                                <div class="text-xs text-gray-500">Include only subjects you teach.</div>
                            </div>
                        </label>

                        <label class="flex items-start space-x-3 p-3 border rounded-md cursor-pointer hover:bg-gray-50">
                            <input type="radio" class="mt-1" value="all" wire:model="reportScope">
                            <div>
                                <div class="text-sm font-medium text-gray-900">Generate overall subject report</div>
                                <div class="text-xs text-gray-500">Include subjects from other teachers in the same grade & section.</div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Continue
                    </button>
                    <button type="button"
                            wire:click="$set('showingReportScopeModal', false)"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

