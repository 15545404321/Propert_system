Vue.component('Glchewei', {
	template: `
		<el-dialog title="关联车位" width="800px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="车位信息" prop="cewei_id">
							<treeselect @deselect="deselectcewei_id" v-if="show" :flat="true" :appendToBody="true" :default-expand-level="2" multiple v-model="form.cewei_id" :options="cewei_ids" :normalizer="normalizer" :show-count="true" zIndex="999999" placeholder="请选择车位信息"/>
						</el-form-item>
					</el-col>
				</el-row>
			</el-form>
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
	components:{
		'treeselect':VueTreeselect.Treeselect,
	},
	props: {
		show: {
			type: Boolean,
			default: false
		},
		size: {
			type: String,
			default: 'small'
		},
		info: {
			type: Object,
		},
	},
	data(){
		return {
			form: {
				cewei_id:[],
			},
			zjlx_ids:[],
			khlb_ids:[],
			khlx_ids:[],
			member_idxs:[],
			cewei_ids:[],
			car_ids:[],
			loading:false,
			rules: {
			}
		}
	},
	watch:{
		show(val){
			if(val){
				axios.post(base_url + '/Member/getFieldList').then(res => {
					if(res.data.status == 200){
						this.zjlx_ids = res.data.data.zjlx_ids
						this.khlb_ids = res.data.data.khlb_ids
						this.khlx_ids = res.data.data.khlx_ids
						this.member_idxs = res.data.data.member_idxs
						this.cewei_ids = res.data.data.cewei_ids
						this.car_ids = res.data.data.car_ids
					}
				})
			}
		}
	},
	methods: {
		open(){
			this.form = this.info
			this.setDefaultVal('cewei_id')
			if(this.info.pid == '0' ){
				this.$delete(this.info,'pid')
			}
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					axios.post(base_url + '/Member/glchewei',this.form).then(res => {
						if(res.data.status == 200){
							this.$message({message: res.data.msg, type: 'success'})
							this.$emit('refesh_list')
							this.closeForm()
						}else{
							this.loading = false
							this.$message.error(res.data.msg)
						}
					}).catch(()=>{
						this.loading = false
					})
				}
			})
		},
		setDefaultVal(key){
			if(this.form[key] == null || this.form[key] == ''){
				this.form[key] = []
			}
		},
		normalizer(node) {
			if (node.children && !node.children.length) {
				delete node.children
			}
			return {
				id: node.val,
				label: node.key,
				children: node.children
			}
		},
		deselectcewei_id(node){
			const arr = this.form.cewei_id
			arr.splice(arr.findIndex(item => item == node.val),1)
		},
		deselectcar_id(node){
			const arr = this.form.car_id
			arr.splice(arr.findIndex(item => item == node.val),1)
		},
		closeForm(){
			this.$emit('update:show', false)
			this.loading = false
			if (this.$refs['form']!==undefined) {
				this.$refs['form'].resetFields()
			}
		},
	}
})
