export default function(val) {
  return (val === '') ? true : /(^\d{5}$)|(^\d{5}-\d{4}$)/.test(val)
}
