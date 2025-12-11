import * as React from "react"

import { cn } from "@/lib/utils"

function Input({ className, type, children, onChange, value, name, ...rest }) {
  const [displayValue, setDisplayValue] = React.useState("")
  const [cleanValue, setCleanValue] = React.useState("")

  const formatNumber = (val: string) => {
    if (!val) return ""

    const hasTrailingComma = val.endsWith(',')
    const parts = val.split(',')
    const integerPart = parts[0].replace(/\D/g, "")
    const decimalPart = parts[1] !== undefined ? parts[1].replace(/\D/g, "") : null

    if (!integerPart && decimalPart === null) return ""

    const formattedInteger = integerPart ? integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ".") : "0"

    if (decimalPart !== null) {
      return `${formattedInteger},${decimalPart}`
    } else if (hasTrailingComma) {
      return `${formattedInteger},`
    } else {
      return formattedInteger
    }
  }

  const getCleanValue = (val: string) => {
    return val.replace(/\./g, "").replace(/,/g, ".")
  }

  React.useEffect(() => {
    if (type === "number" && value !== undefined) {
      const stringValue = String(value)
      const displayFormat = stringValue.replace('.', ',')
      const clean = getCleanValue(displayFormat)
      setCleanValue(clean)
      setDisplayValue(formatNumber(displayFormat))
    }
  }, [value, type])

  const handleChange = (e) => {
    if (type === "number") {
      const inputValue = e.target.value

      if (inputValue === "") {
        setCleanValue("")
        setDisplayValue("")
        if (onChange) {
          const syntheticEvent = {
            ...e,
            target: {
              ...e.target,
              name: name || e.target.name,
              value: "",
            },
          }
          onChange(syntheticEvent)
        }
        return
      }

      const formatted = formatNumber(inputValue)
      const clean = getCleanValue(formatted)

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
        }
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
