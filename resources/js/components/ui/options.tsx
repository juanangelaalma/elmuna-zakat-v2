import * as React from "react"

import { cn } from "@/lib/utils"

function Options({ className, type, children, ...rest }: React.ComponentProps<"input">) {
  return (
      <div className="relative">
          <Package className="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" />
          <select
              id="rice_item_id"
              value={data.rice_item_id}
              onChange={(e) => setData('rice_item_id', e.target.value)}
              className={`block w-full pl-10 pr-4 py-2.5 border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all ${
                  errors.rice_item_id
                      ? 'border-red-500 focus:ring-red-500'
                      : 'border-gray-300 dark:border-gray-600'
              }`}
          >
          </select>
      </div>
  )
}

export { Input }
