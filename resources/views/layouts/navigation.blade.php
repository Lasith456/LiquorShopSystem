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

        {{-- CATEGORIES --}}
        <li x-data="{ open: {{ request()->routeIs('categories.*') ? 'true' : 'false' }} }">
            <button @click="open = !open"
                class="w-full flex items-center gap-x-3 rounded-md p-2 text-sm font-semibold text-left transition
                {{ request()->routeIs('categories.*') ? 'bg-gray-800 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                <i class="fa-solid fa-layer-group h-5 w-5"></i>
                <span x-show="!sidebarCollapsed || sidebarHover" class="whitespace-nowrap flex-1">Categories</span>
                <i x-show="!sidebarCollapsed || sidebarHover" 
                   :class="{'rotate-90': open}" 
                   class="fa-solid fa-chevron-right text-xs transition-transform duration-300"></i>
            </button>

            <ul x-show="open" x-collapse class="mt-1 px-2 space-y-1">
                <li><a href="{{ route('categories.create') }}" class="block rounded-md py-2 pl-9 pr-2 text-sm hover:bg-gray-800 hover:text-white">Add Category</a></li>
                <li><a href="{{ route('categories.index') }}" class="block rounded-md py-2 pl-9 pr-2 text-sm hover:bg-gray-800 hover:text-white">All Categories</a></li>
            </ul>
        </li>

        {{-- SIZES --}}
        <li x-data="{ open: {{ request()->routeIs('sizes.*') ? 'true' : 'false' }} }">
            <button @click="open = !open"
                class="w-full flex items-center gap-x-3 rounded-md p-2 text-sm font-semibold text-left transition
                {{ request()->routeIs('sizes.*') ? 'bg-gray-800 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                <i class="fa-solid fa-ruler-combined h-5 w-5"></i>
                <span x-show="!sidebarCollapsed || sidebarHover" class="whitespace-nowrap flex-1">Sizes</span>
                <i x-show="!sidebarCollapsed || sidebarHover" 
                   :class="{'rotate-90': open}" 
                   class="fa-solid fa-chevron-right text-xs transition-transform duration-300"></i>
            </button>

            <ul x-show="open" x-collapse class="mt-1 px-2 space-y-1">
                <li><a href="{{ route('sizes.create') }}" class="block rounded-md py-2 pl-9 pr-2 text-sm hover:bg-gray-800 hover:text-white">Add Size</a></li>
                <li><a href="{{ route('sizes.index') }}" class="block rounded-md py-2 pl-9 pr-2 text-sm hover:bg-gray-800 hover:text-white">All Sizes</a></li>
            </ul>
        </li>

        {{-- STOCK --}}
        <li x-data="{ open: false }">
            <button @click="open = !open"
                class="w-full flex items-center gap-x-3 rounded-md p-2 text-sm font-semibold text-left transition hover:bg-gray-800 hover:text-white">
                <i class="fa-solid fa-boxes-packing h-5 w-5"></i>
                <span x-show="!sidebarCollapsed || sidebarHover" class="whitespace-nowrap flex-1">Stock</span>
                <i x-show="!sidebarCollapsed || sidebarHover" 
                   :class="{'rotate-90': open}" 
                   class="fa-solid fa-chevron-right text-xs transition-transform duration-300"></i>
            </button>

            <ul x-show="open" x-collapse class="mt-1 px-2 space-y-1">
                <li><a href="{{ route('stocks.create') }}" class="block rounded-md py-2 pl-9 pr-2 text-sm hover:bg-gray-800 hover:text-white">Add Stock</a></li>
                <li><a href="{{ route('stocks.index') }}" class="block rounded-md py-2 pl-9 pr-2 text-sm hover:bg-gray-800 hover:text-white">All Stocks</a></li>
                <li><a href="{{ route('sells.create') }}" class="block rounded-md py-2 pl-9 pr-2 text-sm hover:bg-gray-800 hover:text-white">Add Sell Stock</a></li>
                <li><a href="{{ route('sells.index') }}" class="block rounded-md py-2 pl-9 pr-2 text-sm hover:bg-gray-800 hover:text-white">All Sell Stocks</a></li>
                <li><a href="{{ route('bottles.create') }}" class="block rounded-md py-2 pl-9 pr-2 text-sm hover:bg-gray-800 hover:text-white">Return Bottle</a></li>
                <li><a href="{{ route('bottles.index') }}" class="block rounded-md py-2 pl-9 pr-2 text-sm hover:bg-gray-800 hover:text-white">Return All Bottles</a></li>


            </ul>
        </li>

        {{-- 📊 REPORTS --}}
        <li x-data="{ open: false }">
            <button @click="open = !open"
                class="w-full flex items-center gap-x-3 rounded-md p-2 text-sm font-semibold text-left transition hover:bg-gray-800 hover:text-white">
                <i class="fa-solid fa-chart-line h-5 w-5"></i>
                <span x-show="!sidebarCollapsed || sidebarHover" class="whitespace-nowrap flex-1">Reports</span>
                <i x-show="!sidebarCollapsed || sidebarHover" 
                :class="{'rotate-90': open}" 
                class="fa-solid fa-chevron-right text-xs transition-transform duration-300"></i>
            </button>

            {{-- Dropdown list --}}
            <ul x-show="open" x-collapse class="mt-1 px-2 space-y-1">

                {{-- 📅 Day-wise Sales --}}
                <li>
                    <a href="{{ route('reports.daywise') }}"
                    class="block rounded-md py-2 pl-9 pr-2 text-sm hover:bg-gray-800 hover:text-white">
                        📅 Day-wise Sales
                    </a>
                </li>

                {{-- 📆 Monthly Sales --}}
                <li>
                    <a href="{{ route('reports.monthly') }}"
                    class="block rounded-md py-2 pl-9 pr-2 text-sm hover:bg-gray-800 hover:text-white">
                        📆 Monthly Sales
                    </a>
                </li>

                {{-- 🧾 Product-wise Sales --}}
                <li>
                    <a href="{{ route('reports.productwise') }}"
                    class="block rounded-md py-2 pl-9 pr-2 text-sm hover:bg-gray-800 hover:text-white">
                        🧾 Product-wise Sales
                    </a>
                </li>

                {{-- 📦 Stock Summary --}}
                <li>
                    <a href="{{ route('reports.stocksummary') }}"
                    class="block rounded-md py-2 pl-9 pr-2 text-sm hover:bg-gray-800 hover:text-white">
                        📦 Stock Summary
                    </a>
                </li>
                  {{-- 📦 Stock Added --}}
                <li>
                    <a href="{{ route('reports.stockadded') }}"
                    class="block rounded-md py-2 pl-9 pr-2 text-sm hover:bg-gray-800 hover:text-white">
                        📦 Stock Added
                    </a>
                </li>
                {{-- 🍾 Bottle Return --}}
                <li>
                    <a href="{{ route('reports.bottles') }}"
                    class="block rounded-md py-2 pl-9 pr-2 text-sm hover:bg-gray-800 hover:text-white">
                        🍾 Bottles
                    </a>
                </li>
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
