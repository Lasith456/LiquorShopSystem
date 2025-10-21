<nav class="flex flex-1 flex-col w-full text-gray-400">
    <ul role="list" class="flex flex-1 flex-col gap-y-7 no-scrollbar overflow-y-auto">

        {{-- DASHBOARD --}}
        <li>
            <a href="{{ route('home') }}"
                class="{{ request()->routeIs('home') ? 'bg-gray-800 text-white' : 'hover:bg-gray-800 hover:text-white' }} 
                       group flex items-center gap-x-3 rounded-md p-2 text-sm font-semibold transition">
                <i class="fa-solid fa-house h-5 w-5"></i>
                <span x-show="!sidebarCollapsed || sidebarHover" class="whitespace-nowrap">Dashboard</span>
            </a>
        </li>

        {{-- PRODUCTS --}}
        <li x-data="{ open: {{ request()->routeIs('products.*') ? 'true' : 'false' }} }">
            <button @click="open = !open"
                class="w-full flex items-center gap-x-3 rounded-md p-2 text-sm font-semibold text-left transition
                {{ request()->routeIs('products.*') ? 'bg-gray-800 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                <i class="fa-solid fa-boxes-stacked h-5 w-5"></i>
                <span x-show="!sidebarCollapsed || sidebarHover" class="whitespace-nowrap flex-1">Products</span>
                <i x-show="!sidebarCollapsed || sidebarHover" 
                   :class="{'rotate-90': open}" 
                   class="fa-solid fa-chevron-right text-xs transition-transform duration-300"></i>
            </button>

            <ul x-show="open" x-collapse class="mt-1 px-2 space-y-1">
                <li><a href="{{ route('products.create') }}" class="block rounded-md py-2 pl-9 pr-2 text-sm hover:bg-gray-800 hover:text-white">Add Product</a></li>
                <li><a href="{{ route('products.index') }}" class="block rounded-md py-2 pl-9 pr-2 text-sm hover:bg-gray-800 hover:text-white">All Products</a></li>
            </ul>
        </li>

        {{-- USERS --}}
        <li x-data="{ open: {{ request()->routeIs('users.*') || request()->routeIs('roles.*') ? 'true' : 'false' }} }">
            <button @click="open = !open"
                class="w-full flex items-center gap-x-3 rounded-md p-2 text-sm font-semibold text-left transition
                {{ request()->routeIs('users.*') || request()->routeIs('roles.*') ? 'bg-gray-800 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                <i class="fa-solid fa-users-gear h-5 w-5"></i>
                <span x-show="!sidebarCollapsed || sidebarHover" class="whitespace-nowrap flex-1">Users</span>
                <i x-show="!sidebarCollapsed || sidebarHover" 
                   :class="{'rotate-90': open}" 
                   class="fa-solid fa-chevron-right text-xs transition-transform duration-300"></i>
            </button>

            <ul x-show="open" x-collapse class="mt-1 px-2 space-y-1">
                <li><a href="{{ route('users.index') }}" class="block rounded-md py-2 pl-9 pr-2 text-sm hover:bg-gray-800 hover:text-white">Manage Users</a></li>
                <li><a href="{{ route('roles.index') }}" class="block rounded-md py-2 pl-9 pr-2 text-sm hover:bg-gray-800 hover:text-white">Manage Roles</a></li>
            </ul>
        </li>

        {{-- LOGOUT --}}
        <li class="mt-auto border-t border-gray-700 pt-4">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-x-3 rounded-md p-2 text-sm font-semibold hover:bg-gray-800 hover:text-white transition">
                    <i class="fa-solid fa-right-from-bracket h-5 w-5"></i>
                    <span x-show="!sidebarCollapsed || sidebarHover" class="whitespace-nowrap">Logout</span>
                </button>
            </form>
        </li>

    </ul>
</nav>
