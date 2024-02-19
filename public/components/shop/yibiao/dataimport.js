Vue.component('dataimport', {
	template: `
	<el-dialog title="导入excel" style="margin-top:100px;" width="600px" :visible.sync="show" @close="closeForm" append-to-body>
		<el-upload v-if="!process" class="upload-demo" action :auto-upload="false" :show-file-list="false" :on-change="choose_file">
			<el-button size="mini" icon="el-icon-upload" type="primary">请选择导入excel</el-button> <span style="color:#ff0000">{{file.name}}</span>
		</el-upload>
		<el-progress v-else :percentage="percentage"></el-progress>
		<div slot="footer" class="dialog-footer">
			<el-button :size="size" :loading="loading" type="primary" @click="submit" >
				<span v-if="!loading">确 定</span>
				<span v-else>提 交 中...</span>
			</el-button>
			<el-button :size="size" @click="closeForm">取 消</el-button>
		</div>
	</el-dialog>
	`
	,
	props: {
		show: {
			type: Boolean,
			default: true
		},
		size: {
			type: String,
			default: 'mini'
		},
		import_url:{
			type:String
		}
	},
	data() {
		return {
			file: "",
			process:false,
			loading:false,
			excel_import_data:[],
			percentage:0,
			page:1,
			limit:10000,
		}
	},
	methods: {
		choose_file(file) {
			this.file = file
			this.importExcel(file)
		},
		importExcel(file) {
			var excelData = []
			const fileReader = new FileReader()
			fileReader.onload = (ev) => {
				try{
					const data = ev.target.result
					const workbook = XLSX.read(data, { type: "binary" })
					Object.keys(workbook.Sheets).forEach((sheet) => {
						excelData.push(
							...XLSX.utils.sheet_to_json(workbook.Sheets[sheet])
						)
					})
					this.excel_import_data = excelData
				}catch(e){
					this.$message.danger('文件类型不正确')
				}
			}
			fileReader.readAsBinaryString(file.raw)
		},
		submit(){
			this.process = true
			this.loading = true
			let data = this.getData()
			let total_page = Math.ceil(this.excel_import_data.length/this.limit)
			this.percentage = Math.ceil(this.page*100/total_page)
			axios.post(base_url+this.import_url,data).then(res => {
				if(res.data.status == 200){
					this.$message({message: '导入完成', type: 'success'})
					this.$emit('refesh_list')
					this.closeForm()
					/*if(this.page <= total_page-1){
						this.page = this.page +1
						this.submit()
					}else{
						this.$message({message: '导入完成', type: 'success'})
						this.$emit('refesh_list')
						this.closeForm()
					}*/
				}
				window.location.href=base_url+'/YiBiao/dumpDataError';
			}).catch(()=>{
				this.loading = false
			})
		},
		getData(){
			let perdata = []
			for(let i=(this.page-1)*this.limit; i<this.page*this.limit; i++){
				if(this.excel_import_data[i]){
					perdata.push(this.excel_import_data[i])
				}
			}
			return perdata
		},
		closeForm(){
			this.$emit('update:show', false)
			this.file = ''
			this.process = false
			this.percentage = 0
			this.loading = false
			this.page = 1
			this.limit = 1
		}
	}
});