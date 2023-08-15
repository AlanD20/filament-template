<footer class="mt-8 flex justify-between gap-2">

    {{-- <img
        class="w-32 h-16"
        src="{{ asset('/images/dark-logo.svg') }}"
        alt="logo"
    > --}}

    <div class="flex items-center gap-4">

        <div class="flex flex-col gap-1 justify-self-center text-right">
            <span class="block">
                {{ __('reports.footer.address') }}
            </span>
            <span class="block">
                {{ __('reports.footer.tel') }}
            </span>
            <span class="block">
                {{ __('reports.footer.email') }}
            </span>
            <span class="block">
                {{ __('reports.footer.web') }}
            </span>
        </div>

        <div
            class="flex h-full w-2 flex-col"
            style="width:8px;"
        >
            <div
                class="!print-color-exact h-1/2 w-full !bg-[#395623]"
                style="background-color: rgb(57 86 35) !important;-webkit-print-color-adjust: exact !important;print-color-adjust: exact !important;"
            ></div>
            <div
                class="!print-color-exact h-1/2 w-full !bg-[#757070]"
                style="background-color: rgb(117 112 112) !important;-webkit-print-color-adjust: exact !important;print-color-adjust: exact !important;"
            ></div>
        </div>
    </div>
</footer>
