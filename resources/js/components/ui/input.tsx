import * as React from "react"

import { cn } from "@/lib/utils"

function Input({ className, type, children, onChange, value, name, ...rest }: React.ComponentProps<"input">) {
  const [displayValue, setDisplayValue] = React.useState<string>("")
  const [cleanValue, setCleanValue] = React.useState<string>("")

  const formatNumber = (val: string): string => {
    const cleanVal = val.replace(/\D/g, "")

    if (!cleanVal) return ""
    return cleanVal.replace(/\B(?=(\d{3})+(?!\d))/g, ".")
  }

  const getCleanValue = (val: string): string => {
    return val.replace(/\./g, "")
  }

  React.useEffect(() => {
    if (type === "number" && value !== undefined) {
      const stringValue = String(value)
      const clean = getCleanValue(stringValue)
      setCleanValue(clean)
      setDisplayValue(formatNumber(stringValue))
    }
  }, [value, type])

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (type === "number") {
      const inputValue = e.target.value
      const clean = getCleanValue(inputValue)
      const formatted = formatNumber(clean)

      setCleanValue(clean)
      setDisplayValue(formatted)

      if (onChange) {
        const syntheticEvent = {
          ...e,
          target: {
            ...e.target,
            name: name || e.target.name,
            value: clean,
          },
        } as React.ChangeEvent<HTMLInputElement>

        onChange(syntheticEvent)
      }
    } else {
      if (onChange) {
        onChange(e)
      }
    }
  }

  return (
    <div className="relative">
      {type === "number" && name && (
        <input
          type="hidden"
          name={name}
          value={cleanValue}
        />
      )}
      <input
        type={type === "number" ? "text" : type}
        name={type === "number" ? undefined : name}
        data-slot="input"
        className={cn(
          "border-1 border-gray-300 focus:border-indigo-300 file:text-foreground placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground flex h-12 w-full min-w-0 rounded-md border bg-transparent px-3 py-0 text-base shadow-xs transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm",
          "focus-visible:border-indigo-300 focus-visible:ring-indigo-300 focus-visible:ring-[1px] focus-visible:ring-offset-0",
          "aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive",
          className
        )}
        value={type === "number" ? displayValue : value}
        onChange={handleChange}
        {...rest}
      />
      {children}
    </div>
  )
}

export { Input }
