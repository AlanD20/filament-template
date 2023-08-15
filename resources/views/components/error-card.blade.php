<x-layouts.app :title='__("errors.{$code}.title")'>

    <a
        class="flex flex-col items-center justify-center gap-4"
        href="/"
    >
        <div
            class="break-word flex flex-col items-center justify-center overflow-hidden rounded-md bg-white p-8 py-14 shadow-lg transition-all duration-150 ease-in hover:-translate-y-2 hover:scale-95 hover:cursor-pointer hover:shadow-xl [&_*]:select-none">
            <span class="text-3xl font-bold uppercase text-danger-500">
                {{ __("errors.{$code}.page_title") }}
            </span>
            <span class="mt-3 text-lg">
                {{ __("errors.{$code}.description") }}
            </span>
        </div>
    </a>
</x-layouts.app>
