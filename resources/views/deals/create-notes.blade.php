<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight">Create a Deal</h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto">
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h3 class="text-lg font-semibold mb-2">
                        Add instructions
                        @if($isCustomDeal)
                            <span class="text-error">*</span>
                        @else
                            <span class="text-base-content/60 font-normal text-sm">(optional)</span>
                        @endif
                    </h3>
                    <p class="text-sm text-base-content/60 mb-5">
                        @if($isCustomDeal)
                            Tell them exactly what you need. This is required for custom deals.
                        @else
                            Tell them what you need. Leave blank if not needed.
                        @endif
                    </p>

                    <form method="POST" action="{{ route('deals.create.notes.store') }}" enctype="multipart/form-data" x-data="{ files: [], removeFile(index) { this.files.splice(index, 1); document.getElementById('attachments').files = new DataTransfer().files; } }">
                        @csrf

                        <div class="mb-4">
                            <textarea
                                id="notes"
                                name="notes"
                                rows="3"
                                class="textarea textarea-bordered w-full"
                                placeholder="Example: Post at 7pm on Friday, tag us @yourbusiness, use hashtag #local"
                                @if($isCustomDeal) required @endif
                            >{{ old('notes') }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-1" />
                        </div>

                        <!-- File Attachments -->
                        <div class="mb-6">
                            <label for="attachments" class="label">
                                <span class="label-text">Attachments <span class="text-base-content/60 font-normal">(optional, max 5 files)</span></span>
                            </label>
                            <input 
                                type="file" 
                                id="attachments"
                                name="attachments[]" 
                                multiple
                                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif"
                                class="file-input file-input-bordered w-full"
                                @change="files = Array.from($event.target.files)"
                            >
                            <label class="label">
                                <span class="label-text-alt text-base-content/60">PDF, DOC, DOCX, JPG, PNG, GIF (max 10MB each)</span>
                            </label>
                            <x-input-error :messages="$errors->get('attachments')" class="mt-1" />
                            <x-input-error :messages="$errors->get('attachments.*')" class="mt-1" />

                            <!-- Selected files preview -->
                            <div x-show="files.length > 0" class="mt-2 space-y-1.5" style="display: none;">
                                <p class="text-xs font-medium">Selected files:</p>
                                <template x-for="(file, index) in files" :key="index">
                                    <div class="flex items-center justify-between p-1.5 bg-base-200 rounded text-xs">
                                        <span class="truncate flex-1" x-text="file.name"></span>
                                        <button 
                                            type="button" 
                                            @click="removeFile(index)"
                                            class="btn btn-ghost btn-xs text-error ml-2 flex-shrink-0"
                                        >
                                            Remove
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="flex justify-between items-center pt-4 border-t border-base-300">
                            <a href="{{ route('deals.create.deadline') }}" class="btn btn-ghost btn-sm">
                                ← Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Continue
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
