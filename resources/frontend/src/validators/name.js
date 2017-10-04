export default function(val) {
  return (val === '') ? true : /^[a-zA-Z\s\.\-,]+$/.test(val)
}
